<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Models;

use App\Classes\MCFConfig;
use App\Classes\Plotters\Mapper;
use App\Classes\Plotters\PlotterInterface;
use App\Events\Worker\JobDoneEvent;
use App\Exceptions\JobException;
use App\Exceptions\PlotterException;
use App\Exceptions\WorkerException;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'mp_jobs';

    protected $casts = [
        'title' => 'string',
        'plotter_alias' => 'string',
        'plots_to_do' => 'int',
        'plots_done' => 'int',
        'disable_workers_start' => 'bool',
        'max_workers' => 'int',
        'disable_events_emitting' => 'bool',
        'pre_command_enabled' => 'bool',
        'pre_command' => 'string',
        'post_command_enabled' => 'bool',
        'post_command' => 'string',
        'arguments' => 'array',
        'use_globals_for' => 'array',
        'cpu_affinity_enable' => 'bool',
        'cpus' => 'array',
        'save_worker_log' => 'bool',
        'number_of_worker_logs' => 'int',
        'remove_oldest' => 'bool',
        'removing_stop_ts' => 'datetime:Y-m-d H:i:s',
        'use_default_executable' => 'boolean',
        'executable' => 'string',
    ];

    /**
     * Events related to this job.
     *
     * @return  HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Worker starts related to this job.
     *
     * @return  HasMany
     */
    public function starts(): HasMany
    {
        return $this->hasMany(Start::class);
    }

    /**
     * Worker currently doing this job.
     *
     * @return  HasMany
     */
    public function workers(): HasMany
    {
        return $this->hasMany(Worker::class);
    }

    /**
     * Get job title.
     *
     * @return  string
     */
    public function title(): string
    {
        return $this->getAttribute('title');
    }

    /**
     * Fill job attributes from request.
     *
     * @param Request $request
     *
     * @return  void
     */
    public function fromRequest(Request $request): void
    {
        foreach (array_keys($this->casts) as $key) {
            $this->setAttribute($key, $request->input($key));
        }
    }

    /**
     * Calculate how many workers of desired quantity can start.
     *
     * @param int $desired
     *
     * @return  int
     */
    public function numberOfWorkersCanStart(int $desired): int
    {
        $max = $this->getMaxWorkers();

        return $max === null ? $desired : min($max, $desired);
    }

    /**
     * Calculate how many workers can be started.
     * Returns number or null for infinite.
     *
     * @return  int|null
     */
    public function getMaxWorkers(): ?int
    {
        if ($this->getAttribute('disable_workers_start')) {
            return 0;
        }

        // how many plots can be done
        $plotsToDo = $this->getAttribute('plots_to_do');

        // how many workers can do this job
        $maxWorkers = $this->getAttribute('max_workers');

        if ($plotsToDo === 0 && $maxWorkers === 0) {
            // unlimited plots quantity can be done by unlimited number of workers
            // so any number of workers can be started skipping next calculations
            return null;
        }

        // get number of currently running workers (number of pending plots)
        $nowWorking = $this->nowWorkingCount();

        // Calc how many plots left. Null for infinite.
        $plotsLeft = $plotsToDo === 0
            ? null
            : $plotsToDo - $this->getAttribute('plots_done') - $nowWorking;

        // Calc how many worker can be started. Null for infinite.
        $workersLeft = $maxWorkers === 0
            ? null
            : $maxWorkers - $nowWorking;

        // infinite plots, limited workers
        if ($plotsLeft === null) {
            return $workersLeft;
        }

        // infinite workers, limited plots
        if ($workersLeft === null) {
            return $plotsLeft;
        }

        return min($plotsLeft, $workersLeft);
    }

    /**
     * Get number of currently running workers.
     *
     * @return  int
     */
    public function nowWorkingCount(): int
    {
        /** @var \Illuminate\Support\Collection $workers */
        $this->loadMissing('workers');

        $workers = $this->getRelation('workers');

        return $workers->count();
    }

    /**
     * Check if events emitting is enabled for this job.
     *
     * @return  bool
     */
    public function isEventsEnabled(): bool
    {
        return !$this->getAttribute('disable_events_emitting');
    }

    /**
     * Start a number of workers for this job.
     *
     * @param int $numberOfWorkers
     *
     * @return  void
     *
     * @throws  BindingResolutionException
     * @throws  PlotterException
     * @throws  JobException
     * @throws  WorkerException
     */
    public function start(int $numberOfWorkers): void
    {
        if ($numberOfWorkers < 1) {
            return;
        }

        for ($i = 1; $i <= $numberOfWorkers; $i++) {
            $worker = $this->createWorker();
            $worker->save();
            $worker->start();
        }
    }

    /**
     * Make worker with all properties.
     *
     * @return  Worker
     *
     * @throws  JobException
     * @throws  PlotterException
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function createWorker(): Worker
    {
        return Worker::factory(
            $this->getAttribute('id'),
            $this->composePlotterAlias(),
            $this->composeExecutable(),
            $this->composeArguments(),
            $this->getAttribute('cpu_affinity_enable'),
            $this->getAttribute('cpus'),
            $this->isLogForNewWorkerSaved(),

        );
    }

    /**
     * Get plotter alias associated to job.
     *
     * @return  string
     *
     * @throws  JobException
     *
     * @internal
     */
    protected function composePlotterAlias(): string
    {
        if (($alias = $this->getAttribute('plotter_alias')) === null) {
            throw new JobException(sprintf('[%s] Has no proper plotter assigned to job', static::class));
        }

        return $alias;
    }

    /**
     * Get executable path for job associated plotter.
     *
     * @return  string
     *
     * @throws  JobException
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function composeExecutable(): string
    {
        $executable = $this->getAttribute('executable');

        if (empty($executable)) {
            /** @var MCFConfig $config */
            $config = app()->make(MCFConfig::class);
            $plotterAlias = $this->composePlotterAlias();
            $executable = $config->get("plotting.plotters.$plotterAlias.executable");
        }

        if (empty($executable)) {
            throw new JobException(sprintf('[%s] Has no proper plotter executable assigned to job', static::class));
        }

        return $executable;
    }

    /**
     * Compose array of command arguments must be passed to worker with global overrides conditions.
     *
     * @return  array
     *
     * @throws  JobException
     * @throws  PlotterException
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function composeArguments(): array
    {
        // get job stored arguments
        $arguments = $this->getAttribute('arguments');

        // get arguments must be overridden by globals
        $toFetchFromGlobals = $this->getAttribute('use_globals_for');

        // get plotter alias
        $plotterAlias = $this->composePlotterAlias();

        // and class of plotter to read global keys override
        /** @var PlotterInterface $plotterClass */
        $plotterClass = Mapper::getClassOf($plotterAlias);

        // get associations of plotter arguments with global config keys
        $associations = $plotterClass::getGlobalDefaultsAssociations();

        /** @var MCFConfig $config */
        $config = app()->make(MCFConfig::class);

        // Replace all job stored arguments what must be overridden by globals.
        // If globals is not set for some of those arguments it will be overwritten by null.
        foreach ($toFetchFromGlobals as $key => $enabled) {
            if (!$enabled) {
                continue;
            }
            // try lo fetch attribute from plotter defaults first
            if ($config->has("plotting.plotters.$plotterAlias.arguments.$key")) {
                $arguments[$key] = $config->get("plotting.plotters.$plotterAlias.arguments.$key");
                continue;
            }
            // and from global defaults second
            $globalKey = $associations[$key] ?? null;
            $arguments[$key] = $globalKey !== null
                ? $config->get($globalKey)
                : null;
        }

        return $arguments;
    }

    /**
     * Check and update if new worker must save log after job is done.
     *
     * @return  bool
     *
     * @internal
     */
    protected function isLogForNewWorkerSaved(): bool
    {
        if (!$this->getAttribute('save_worker_log')) {
            return false;
        }

        $number = $this->getAttribute('number_of_worker_logs');

        if ($number === 0) {
            return true;
        }

        if ($number >= 1) {
            $number--;
            $this->setAttribute('number_of_worker_logs', $number);
            $this->setAttribute('save_worker_monitor_log', $number !== 0);
            $this->save();

            return true;
        }

        return false;
    }

    /**
     * Get pre-process command for this job.
     *
     * @return  string|null
     */
    public function preProcessCommand(): ?string
    {
        return $this->getCommand('pre_command_enabled', 'pre_command');
    }

    /**
     * Get post-process command for this job.
     *
     * @return  string|null
     */
    public function postProcessCommand(): ?string
    {
        return $this->getCommand('post_command_enabled', 'post_command');

    }

    /**
     * Make command if it is not empty and enabled, null otherwise.
     *
     * @param string $enabledAttr
     * @param string $commandAttr
     *
     * @return  string|null
     *
     * @internal
     */
    protected function getCommand(string $enabledAttr, string $commandAttr): ?string
    {
        if (!$this->getAttribute($enabledAttr)) {
            return null;
        }

        return $this->getAttribute($commandAttr);
    }

    /**
     * Increase plots done counter and check last worker finished job to fire job done event.
     *
     * @return  void
     */
    public function iterationDone(): void
    {
        $done = $this->getAttribute('plots_done') + 1;
        $this->setAttribute('plots_done', $done);
        $this->save();

        // check if job ended

        // if number of plots to be done is infinite there is no job end.
        if (($toDo = $this->getAttribute('plots_to_do')) <= 0) {
            return;
        }

        // if number of plots is reached target and there is only one worker at this time
        // that actually activated this method by emitting WorkerDoneEvent and will be shutdown soon
        // means worker has done last job iteration
        if ($toDo === $done && $this->nowWorkingCount() !== 1) {
            JobDoneEvent::dispatch($this->getAttribute('id'));
        }
    }

    /**
     * Returns date limit of replotting if it set or null otherwise.
     *
     * @return  Carbon|null
     */
    public function getRePlotLimit(): ?Carbon
    {
        if ($this->getAttribute('remove_oldest') !== true) {
            return null;
        }

        return Carbon::parse($this->getAttribute('removing_stop_ts'));
    }
}
