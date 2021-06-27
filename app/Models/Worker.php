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

use App\Traits\SystemCommandsTrait;
use App\Classes\Plotters\Mapper;
use App\Classes\Plotters\PlotterInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Exceptions\WorkerException;
use App\Exceptions\PlotterException;
use App\Exceptions\SystemCommandException;
use Illuminate\Contracts\Container\BindingResolutionException;

use App\Events\Helpers\WorkerBaseEvent;
use App\Events\Worker\PlottingFinishedEvent;
use App\Events\Worker\PlottingStartedEvent;
use App\Events\Worker\PostProcessFinishedEvent;
use App\Events\Worker\PostProcessStartedEvent;
use App\Events\Worker\PreProcessFinishedEvent;
use App\Events\Worker\PreProcessStartedEvent;
use App\Events\Worker\WorkerDoneEvent;
use App\Events\WorkerStateEvent;
use App\Events\ReplottingEvent;

class Worker extends Model
{
    use SystemCommandsTrait;

    protected $table = 'mp_workers';

    protected $casts = [
        'pid' => 'int',
        'executable' => 'string',
        'phase' => 'int',
        'step' => 'int',
        'percents' => 'int',
        'attributes' => 'array',
        'cpu_affinity_enable' => 'bool',
        'cpus' => 'array',
        'save_log' => 'bool',
        'running_pre_command' => 'bool',
        'running_post_command' => 'bool',
        'has_error' => 'bool',
        'error' => 'string',
        'status' => 'string',
        'created_at' => 'datetime:Y-m-d H:i:s'
    ];

    /** @var PlotterInterface Plotter cache */
    protected PlotterInterface $plotter;

    /**
     * Related job.
     *
     * @return  BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get associated job.
     *
     * @return  Job
     */
    protected function getJob(): Job
    {
        return $this->loadMissing('job')->getRelation('job');
    }

    /**
     * Make plotter for this worker.
     *
     * @return  PlotterInterface
     *
     * @throws  PlotterException
     */
    protected function plotter(): PlotterInterface
    {
        if (!$this->exists) {
            throw new WorkerException(sprintf('[%s] Can not create plotter for non existing worker', static::class));
        }

        if (!isset($this->plotter)) {
            $this->plotter = Mapper::make(
                $this->getAttribute('plotter_alias'),
                $this->getAttribute('id'),
                $this->getAttribute('executable'),
                $this->getAttribute('attributes'),
                $this->getAttribute('cpu_affinity_enable'),
                $this->getAttribute('cpus'),
                $this->logFileName()
            );
        }

        return $this->plotter;
    }

    /**
     * Worker factory.
     *
     * @param int $jobId
     * @param string $plotterAlias
     * @param string $executable
     * @param array $attributes
     * @param bool $cpuAffinityEnable
     * @param array|null $cpus
     * @param bool $saveLog
     *
     * @return  Worker
     */
    public static function factory(int $jobId, string $plotterAlias, string $executable, array $attributes, bool $cpuAffinityEnable, ?array $cpus, bool $saveLog): Worker
    {
        $worker = new Worker;

        $worker->setAttribute('job_id', $jobId);
        $worker->setAttribute('plotter_alias', $plotterAlias);
        $worker->setAttribute('executable', $executable);
        $worker->setAttribute('phase', 0);
        $worker->setAttribute('step', 0);
        $worker->setAttribute('percents', 0);
        $worker->setAttribute('attributes', $attributes);
        $worker->setAttribute('cpu_affinity_enable', $cpuAffinityEnable);
        $worker->setAttribute('cpus', $cpus);
        $worker->setAttribute('save_log', $saveLog);

        return $worker;
    }

    /**
     * Worker status.
     *
     * @return  string|null
     */
    public function status(): ?string
    {
        return $this->getAttribute('status');
    }

    /**
     * Start this worker.
     *
     * @return  bool
     *
     * @throws  WorkerException
     * @throws  BindingResolutionException
     * @throws  PlotterException
     */
    public function start(): bool
    {
        try {
            $this->checkStartConditions();

            // handle pre command
            if (($preProcessCommand = $this->getJob()->preProcessCommand()) !== null) {

                // if pre-process command enabled test plotter first
                $this->plotter()->test();

                // set states and run command in background
                $this->setCommandState(true);
                $pid = $this->systemCommands()->runInBackground($preProcessCommand, $this->logFileName('pre'));
                $this->setAttribute('pid', $pid);
                $this->setAttribute('status', 'Running pre-process command');

                $this->save();

                Log::debug(sprintf('Worker [%s] started pre-process command. PID: %s', $this->getAttribute('id'), $pid));

                $this->fire(PreProcessStartedEvent::class);

                return true;
            }

            $this->startPlotting();

        } catch (PlotterException | WorkerException | SystemCommandException | BindingResolutionException $exception) {

            $this->shutdown(true, sprintf('[%s] %s', get_class($exception), $exception->getMessage()));

            throw new WorkerException(sprintf('Can not start worker for job [%s]: %s', $this->getAttribute('job_id'), $exception->getMessage()));
        }

        return true;
    }

    /**
     * Set states for pre- and post-process command running.
     *
     * @param bool $pre
     * @param bool $post
     *
     * @return  void
     */
    protected function setCommandState(bool $pre = false, bool $post = false): void
    {
        $this->setAttribute('running_pre_command', $pre);
        $this->setAttribute('running_post_command', $post);
    }

    /**
     * Start plotting process.
     *
     * @throws  PlotterException
     * @throws  SystemCommandException
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function startPlotting(): void
    {
        $this->setCommandState();

        $pid = $this->plotter()->run();
        $this->setAttribute('pid', $pid);
        $this->setAttribute('status', 'Starting plotting process');

        $this->save();

        Log::debug(sprintf('Worker [%s] started. PID: %s', $this->getAttribute('id'), $pid));

        $this->fire(PlottingStartedEvent::class);

        ReplottingEvent::dispatch($this->getAttribute('job_id'), $this->plotter()->getDestination());
    }

    /**
     * Get logging absolute filename.
     *
     * @param string|null $postfix
     *
     * @return  string|null
     */
    public function logFileName(?string $postfix = null): ?string
    {
        if (($id = $this->getAttribute('id')) === null) {
            return null;
        }

        return app()->basePath() . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $id . ($postfix ? "_$postfix" : null) . '.txt';
    }

    /**
     * Remove log file.
     *
     * @param string|null $postfix
     *
     * @return  void
     *
     * @internal
     */
    protected function cleanUpLog(?string $postfix = null): void
    {
        if (!$this->getAttribute('save_log') && is_file($log = $this->logFileName($postfix))) {
            unlink($log);
        }
    }

    /**
     * Check start conditions for worker.
     *
     * @return  void
     *
     * @throws  WorkerException
     *
     * @internal
     */
    protected function checkStartConditions(): void
    {
        if (!$this->exists) {
            throw new WorkerException('Worker must be properly created before start.');
        }

        if ($this->getAttribute('pid') !== null) {
            throw new WorkerException('Worker is running already.');
        }
    }

    /**
     * Shutdown and cleanup this worker.
     *
     * @param bool $error
     * @param string|null $message
     *
     * @return  void
     *
     * @throws  BindingResolutionException
     * @throws  PlotterException
     */
    public function shutdown(bool $error, ?string $message = null): void
    {
        Log::debug(sprintf('Shutting down worker [%s]%s',
            $this->getAttribute('id'),
            $error ? ' with error' : ''
        ));

        if ($error) {
            $this->handleError($message);
        }

        $this->killProcess();

        $this->plotter()->cleanUp($this->getAttribute('save_log'));
        $this->cleanUpLog('pre');
        $this->cleanUpLog('post');

        $this->delete();
    }

    /**
     * Check if process is running.
     *
     * @return  bool
     *
     * @throws  BindingResolutionException
     */
    public function isWorking(): bool
    {
        if (($pid = $this->getAttribute('pid')) === null) {
            return false;
        }

        return $this->systemCommands()->isProcessRunning($pid);
    }

    /**
     * Kill process if it is running.
     *
     * @return  void
     *
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function killProcess(): void
    {
        if (($pid = $this->getAttribute('pid')) === null) {
            return;
        }

        if ($this->systemCommands()->isProcessRunning($pid)) {
            $this->systemCommands()->killProcess($pid);
        }
    }

    /**
     * Handle worker error.
     *
     * @param string|null $message
     *
     * @return  void
     */
    protected function handleError(?string $message = 'Something went wrong'): void
    {
        Log::error($message);
    }

    /**
     * Fire event.
     *
     * @param string $event
     *
     * @return  void
     *
     * @internal
     */
    protected function fire(string $event): void
    {
        /** @var WorkerBaseEvent $event */
        $event::dispatch($this->getAttribute('job_id'));
    }

    /**
     * Update worker state.
     *
     * @throws  PlotterException
     * @throws  SystemCommandException
     * @throws  BindingResolutionException
     */
    public function updateState(): void
    {
        if ($this->getAttribute('id') === null) {
            return;
        }

        $stopped = !$this->isWorking();
        $pre = $this->getAttribute('running_pre_command');
        $post = $this->getAttribute('running_post_command');

        // Check if plotting is running parse state from log
        if (!$stopped && !$pre && !$post) {
            $this->parseState();
            return;
        }

        // Process finished
        if ($stopped) {

            // If pre-process command finished start plotting
            if ($pre) {
                $this->fire(PreProcessFinishedEvent::class);
                $this->startPlotting();
                return;
            }

            // If post-process command finished finalize and shutdown worker
            if ($post) {
                $this->fire(PostProcessFinishedEvent::class);
                $this->finalize();
                $this->shutdown(false);
                return;
            }

            // Otherwise plotting finished
            $this->fire(PlottingFinishedEvent::class);

            // fire state changed event to handle last phase ended and 100% is reached
            WorkerStateEvent::dispatch(
                $this->getAttribute('job_id'),
                $phase = $this->getAttribute('phase'), $phase + 1,
                $this->getAttribute('step'), 0,
                $this->getAttribute('percents'), 100
            );

            // Check if job needs post-process command to be executed
            if (($postProcessCommand = $this->getJob()->postProcessCommand()) !== null) {
                // set states and run command in background
                $this->setCommandState(false, true);
                $postProcessCommand = str_replace(['%plot%'], [$this->getAttribute('plot_file_name')], $postProcessCommand);
                $pid = $this->systemCommands()->runInBackground($postProcessCommand, $this->logFileName('post'));
                $this->setAttribute('pid', $pid);
                $this->setAttribute('status', 'Running post-process command');
                $this->save();

                Log::debug(sprintf('Worker [%s] started post-process command. PID: %s', $this->getAttribute('id'), $pid));
                $this->fire(PostProcessStartedEvent::class);
                return;
            }

            // Finalize and shutdown worker if no post-process command
            $this->finalize();
            $this->shutdown(false);
        }
    }

    /**
     * Finalize worker. Make events for phase 5 is done and worker finished the job.
     *
     * @return  void
     *
     * @internal
     */
    protected function finalize(): void
    {
        $this->fire(WorkerDoneEvent::class);

        Log::debug(sprintf('Worker [%s] finalized.', $this->getAttribute('id')));
    }

    /**
     * Update progress of worker and fire event if state has changed.
     *
     * @throws  PlotterException
     *
     * @internal
     */
    protected function parseState(): void
    {
        $state = $this->plotter()->parser()->getProgress();

        $this->setAttribute('status', $state->state());

        // Update plot file name only if it was not set and exists in state.
        if ($state->plotFileName() !== null && $this->getAttribute('plot_file_name') === null) {
            $destination = rtrim($this->plotter()->getDestination(), ' \t\n\r\0\x0B\\/');
            $filename = trim($state->plotFileName(), ' \t\n\r\0\x0B\\/');
            $this->setAttribute('plot_file_name', $destination . DIRECTORY_SEPARATOR . $filename);
        }

        if ($state->hasError()) {
            $this->setAttribute('has_error', $state->plotFileName());
            $this->setAttribute('error', $state->plotFileName());
        } else {
            $this->setAttribute('has_error', false);
            $this->setAttribute('error', null);
        }

        // Save will write attributes to DB only if there is changes.
        $this->save();

        $phase = $this->getAttribute('phase') ?? 0;
        $step = $this->getAttribute('step') ?? 0;
        $progress = $this->getAttribute('percents') ?? 0;

        if ($phase !== $state->phase() || $step !== $state->step() || $progress !== $state->progress()) {
            // update worker state
            $this->setAttribute('phase', $state->phase());
            $this->setAttribute('step', $state->step());
            $this->setAttribute('percents', $state->progress());
            $this->save();

            Log::debug(sprintf('Worker [%s] state changed. Phase %s->%s, step %s->%s, progress %s->%s]',
                $this->getAttribute('id'),
                $phase, $state->phase(),
                $step, $state->step(),
                $progress, $state->progress()
            ));

            // fire worker state changed event if changed
            WorkerStateEvent::dispatch(
                $this->getAttribute('job_id'),
                $phase, $state->phase(),
                $step, $state->step(),
                $progress, $state->progress()
            );
        }
    }
}
