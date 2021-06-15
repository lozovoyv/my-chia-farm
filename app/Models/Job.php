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

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'mp_jobs';

    protected $casts = [
        'disable' => 'bool',
        'use_global_keys' => 'bool',
        'number_of_plots' => 'int',
        'plots_done' => 'int',
        'use_global_plot_size' => 'bool',
        'plot_size' => 'int',
        'use_global_buckets' => 'bool',
        'buckets' => 'int',
        'use_global_buffer' => 'bool',
        'buffer' => 'int',
        'use_global_threads' => 'bool',
        'threads' => 'int',
        'use_global_tmp_dir' => 'bool',
        'use_global_tmp2_dir' => 'bool',
        'use_global_final_dir' => 'bool',
        'use_global_disable_bitfield' => 'bool',
        'disable_bitfield' => 'bool',
        'use_global_skip_add' => 'bool',
        'skip_add' => 'bool',
        'cpu_affinity_enable' => 'bool',
        'cpus' => 'array',
        'events_disable' => 'bool',
        'max_workers' => 'int',
        'save_worker_monitor_log' => 'bool',
        'number_of_worker_logs' => 'int',
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
        if ($this->getAttribute('disable')) return 0;

        // how many plots can be done
        $plotsToDo = $this->getAttribute('number_of_plots');

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
        if ($plotsLeft === null) return $workersLeft;

        // infinite workers, limited plots
        if ($workersLeft === null) return $plotsLeft;

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
     * Start a number of workers for this job.
     *
     * @param int $numberOfWorkers
     *
     * @return  void
     *
     * @throws  BindingResolutionException
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
     * Run worker factory.
     *
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function createWorker(): Worker
    {
        return Worker::factory(
            $this->getAttribute('id'),
            $this->getAttribute('use_global_keys'),
            $this->getAttribute('farmer_public_key'),
            $this->getAttribute('pool_public_key'),
            $this->getAttribute('use_global_plot_size'),
            $this->getAttribute('plot_size'),
            $this->getAttribute('use_global_buckets'),
            $this->getAttribute('buckets'),
            $this->getAttribute('use_global_buffer'),
            $this->getAttribute('buffer'),
            $this->getAttribute('use_global_threads'),
            $this->getAttribute('threads'),
            $this->getAttribute('use_global_tmp_dir'),
            $this->getAttribute('tmp_dir'),
            $this->getAttribute('use_global_tmp2_dir'),
            $this->getAttribute('tmp2_dir'),
            $this->getAttribute('use_global_final_dir'),
            $this->getAttribute('final_dir'),
            $this->getAttribute('use_global_disable_bitfield'),
            $this->getAttribute('disable_bitfield'),
            $this->getAttribute('use_global_skip_add'),
            $this->getAttribute('skip_add'),
            $this->getAttribute('cpu_affinity_enable'),
            $this->getAttribute('cpus'),
            $this->isLogForNewWorkerSaved()
        );
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
        if (!$this->getAttribute('save_worker_monitor_log')) return false;

        $number = $this->getAttribute('number_of_worker_logs');

        if ($number === 0) return true;

        if ($number >= 1) {
            $number--;
            $this->setAttribute('number_of_worker_logs', $number);
            $this->setAttribute('save_worker_monitor_log', $number !== 0);
            $this->save();

            return true;
        }

        return false;
    }
}
