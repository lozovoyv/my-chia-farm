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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use App\Traits\ChiaCommandsTrait;
use App\Traits\SystemCommandsTrait;
use App\Classes\MCFConfig;
use App\Classes\LogParser;
use App\Exceptions\ChiaCommandException;
use App\Exceptions\WorkerException;
use App\Events\WorkerStateEvent;
use App\Events\WorkerFinishedEvent;

class Worker extends Model
{
    use ChiaCommandsTrait,
        SystemCommandsTrait;

    protected $table = 'mp_workers';

    protected $casts = [
        'pid' => 'int',
        'phase' => 'int',
        'step' => 'int',
        'percents' => 'int',
        'plot_size' => 'int',
        'buckets' => 'int',
        'buffer' => 'int',
        'threads' => 'int',
        'disable_bitfield' => 'bool',
        'skip_add' => 'bool',
        'cpu_affinity_enable' => 'bool',
        'cpus' => 'array',
        'save_log' => 'bool',
    ];

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
     * Worker creating factory.
     *
     * @param int $jobId
     * @param bool $globalKeys
     * @param string|null $farmerPublicKey
     * @param string|null $poolPublicKey
     * @param bool $globalPlotSize
     * @param int|null $plotSize
     * @param bool $globalBuckets
     * @param int|null $buckets
     * @param bool $globalBuffer
     * @param int|null $buffer
     * @param bool $globalThreads
     * @param int|null $threads
     * @param bool $globalTmpDir
     * @param string|null $tmpDir
     * @param bool $globalTmp2Dir
     * @param string|null $tmp2Dir
     * @param bool $globalFinalDir
     * @param string|null $finalDir
     * @param bool $globalDisableBitfield
     * @param bool $disableBitfield
     * @param bool $globalSkipAdd
     * @param bool $skipAdd
     * @param bool $cpuAffinityEnable
     * @param array|null $cpus
     * @param bool $saveLog
     *
     * @return  Worker
     *
     * @throws  BindingResolutionException
     */
    public static function factory(int $jobId, bool $globalKeys, ?string $farmerPublicKey, ?string $poolPublicKey, bool $globalPlotSize, ?int $plotSize, bool $globalBuckets, ?int $buckets, bool $globalBuffer, ?int $buffer, bool $globalThreads, ?int $threads, bool $globalTmpDir, ?string $tmpDir, bool $globalTmp2Dir, ?string $tmp2Dir, bool $globalFinalDir, ?string $finalDir, bool $globalDisableBitfield, bool $disableBitfield, bool $globalSkipAdd, bool $skipAdd, bool $cpuAffinityEnable, ?array $cpus, bool $saveLog): Worker
    {
        /** @var MCFConfig $config */
        $config = app()->make(MCFConfig::class);

        $worker = new Worker;

        $worker->setAttribute('job_id', $jobId);
        $worker->setAttribute('phase', 0);
        $worker->setAttribute('step', 0);
        $worker->setAttribute('percents', 0);
        $worker->setAttribute('farmer_public_key', $globalKeys ? $config->get('general.default_farmer_key') : $farmerPublicKey);
        $worker->setAttribute('pool_public_key', $globalKeys ? $config->get('general.default_pool_key') : $poolPublicKey);
        $worker->setAttribute('plot_size', $globalPlotSize ? $config->get('job.default_plot_size') : $plotSize);
        $worker->setAttribute('buckets', $globalBuckets ? $config->get('job.default_buckets') : $buckets);
        $worker->setAttribute('buffer', $globalBuffer ? $config->get('job.default_buffer') : $buffer);
        $worker->setAttribute('threads', $globalThreads ? $config->get('job.default_threads') : $threads);
        $worker->setAttribute('tmp_dir', $globalTmpDir ? $config->get('job.default_tmp_dir') : $tmpDir);
        $worker->setAttribute('tmp2_dir', $globalTmp2Dir ? $config->get('job.default_tmp2_dir') : $tmp2Dir);
        $worker->setAttribute('final_dir', $globalFinalDir ? $config->get('job.default_final_dir') : $finalDir);
        $worker->setAttribute('disable_bitfield', $globalDisableBitfield ? $config->get('job.default_disable_bitfield') : $disableBitfield);
        $worker->setAttribute('skip_add', $globalSkipAdd ? $config->get('job.default_skip_add') : $skipAdd);
        $worker->setAttribute('cpu_affinity_enable', $cpuAffinityEnable);
        $worker->setAttribute('cpus', $cpus);
        $worker->setAttribute('save_log', $saveLog);

        return $worker;
    }

    /**
     * Start this worker.
     *
     * @return  bool
     *
     * @throws  BindingResolutionException
     */
    public function start(): bool
    {
        try {
            $this->createTemporaryDir();

            $this->createSecondaryTemporaryDir();

            $this->checkStartConditions();

            $pid = $this->runPlotCommand();

            $this->setAttribute('pid', $pid);

            $this->save();

            Log::debug(sprintf('Worker [%s] started. PID: %s', $this->getAttribute('id'), $pid));

        } catch (ChiaCommandException | WorkerException $exception) {

            $this->shutdown(true, sprintf('[%s] %s', get_class($exception), $exception->getMessage()));

            return false;
        }

        return true;
    }

    /**
     * Get temporary directory path.
     *
     * @return  string|null
     */
    public function tempDir(): ?string
    {
        if (($id = $this->getAttribute('id')) === null) return null;

        return $this->getAttribute('tmp_dir') . DIRECTORY_SEPARATOR . $id;
    }

    /**
     * Create primary temporary directory.
     *
     * @return  void
     *
     * @throws  WorkerException
     *
     * @internal
     */
    protected function createTemporaryDir(): void
    {
        if (($dir = $this->tempDir()) === null) {
            throw new WorkerException('Temporary directory must be set.');
        }

        if (!mkdir($dir, 0777, true) || !is_dir($dir)) {
            throw new WorkerException('Temporary directory can not be created. Check path and permissions to creating it.');
        }
    }

    /**
     * Get second temporary directory path.
     *
     * @return  string|null
     */
    public function temp2Dir(): ?string
    {
        if (($id = $this->getAttribute('id')) === null) return null;

        $dir = $this->getAttribute('tmp2_dir');

        return $dir === null ? null : $dir . DIRECTORY_SEPARATOR . $id;
    }

    /**
     * Create secondary temporary directory.
     *
     * @return  void
     *
     * @throws  WorkerException
     *
     * @internal
     */
    protected function createSecondaryTemporaryDir(): void
    {
        if (($dir = $this->temp2Dir()) === null) return;

        if (!mkdir($dir, 0777, true) || !is_dir($dir)) {
            throw new WorkerException('Secondary temporary directory can not be created. Check path and permissions to creating it.');
        }
    }

    /**
     * Run plotting command.
     *
     * @return  int
     *
     * @throws  ChiaCommandException
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function runPlotCommand(): int
    {
        return $this->chiaCommands()->makePlot(
            $this->getAttribute('farmer_public_key'),
            $this->getAttribute('pool_public_key'),
            $this->getAttribute('plot_size'),
            $this->getAttribute('buckets'),
            $this->getAttribute('buffer'),
            $this->getAttribute('threads'),
            $this->tempDir(),
            $this->temp2Dir(),
            $this->getAttribute('final_dir'),
            $this->getAttribute('disable_bitfield'),
            $this->getAttribute('skip_add'),
            $this->getAttribute('cpu_affinity_enable'),
            $this->getAttribute('cpus'),
            $this->logFileName(),
        );
    }

    /**
     * Cleanup and remove directory.
     *
     * @param string|null $dir
     *
     * @return  void
     *
     * @internal
     */
    protected function cleanUpDir(?string $dir): void
    {
        if ($dir === null || !is_dir($dir)) return;

        foreach (glob($dir . DIRECTORY_SEPARATOR . "*.*") as $filename) {
            if (is_file($filename)) {
                unlink($filename);
            }
        }

        rmdir($dir);
    }

    /**
     * Get logging absolute filename.
     *
     * @return  string|null
     */
    public function logFileName(): ?string
    {
        if (($id = $this->getAttribute('id')) === null) return null;

        return app()->storagePath() . DIRECTORY_SEPARATOR . 'process' . DIRECTORY_SEPARATOR . $id . '.txt';
    }

    /**
     * Remove log file.
     *
     * @return  void
     *
     * @internal
     */
    protected function cleanUpLog(): void
    {
        if (!$this->getAttribute('save_log') && is_file($this->logFileName())) {
            unlink($this->logFileName());
        }
    }

    /**
     * Check start conditions.
     *
     * @return  void
     *
     * @throws  WorkerException
     *
     * @internal
     */
    protected function checkStartConditions(): void
    {
        if (!$this->exists) throw new WorkerException('Worker must be properly created before start.');

        if ($this->getAttribute('pid') !== null) throw new WorkerException('Worker is running already.');

        $message = 'Worker must have proper';
        $hasError = false;

        if ($this->getAttribute('farmer_public_key') === null) {
            $message .= ' farmer public key';
            $hasError = true;
        }
        if ($this->getAttribute('pool_public_key') === null) {
            $message .= ($hasError ? ',' : '') . ' pool public key';
            $hasError = true;
        }
        if ($this->getAttribute('tmp_dir') === null) {
            $message .= ($hasError ? ',' : '') . ' temporary directory';
            $hasError = true;
        }
        if ($this->getAttribute('final_dir') === null) {
            $message .= ($hasError ? ',' : '') . ' destination directory';
            $hasError = true;
        }

        if ($hasError) throw new WorkerException($message);
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
     */
    public function shutdown(bool $error, ?string $message = null): void
    {
        Log::debug(sprintf('Shutting down worker [%s]%s',
            $this->getAttribute('id'),
            $error ? ' with error ' . $message : ''
        ));
        if ($error) $this->handleError($message);

        $this->killProcess();

        $this->cleanUpDir($this->tempDir());
        $this->cleanUpDir($this->temp2Dir());

        $this->cleanUpLog();

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
        if (($pid = $this->getAttribute('pid')) === null) return false;

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
        if (($pid = $this->getAttribute('pid')) === null) return;

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
     * Update worker state.
     *
     * @throws  BindingResolutionException
     */
    public function updateState(): void
    {
        if ($this->getAttribute('id') === null) return;

        if ($this->isWorking()) {
            // If process is still running parse state from log
            $this->parseState();

            return;
        }

        // Process has finished but worker still exists
        // todo: run custom command after phase 5 is done (another pid, phase 6)

        // finalize
        $this->finalize();
        Log::debug(sprintf('Worker [%s] finalized.', $this->getAttribute('id')));

        // and run normal shutdown
        $this->shutdown(false);

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
        // fire phase 6 and 100% event to make phase 5 ended and 100% is reached
        WorkerStateEvent::dispatch(
            $this->getAttribute('job_id'),
            $this->getAttribute('phase'), 6,
            $this->getAttribute('step'), 0,
            $this->getAttribute('percents'), 100
        );

        // fire finished event
        WorkerFinishedEvent::dispatch($this);
    }

    /**
     * Update progress of worker and fire event if state has changed.
     *
     * @throws  BindingResolutionException
     *
     * @internal
     */
    protected function parseState(): void
    {
        /** @var LogParser $parser */
        $parser = app()->make(LogParser::class, [
            'filename' => $this->logFileName(),
            'buckets' => $this->getAttribute('buckets'),
        ]);

        $progress = $parser->getProgress();

        $newPhase = $progress['phase'] ?? 0;
        $newStep = $progress['step'] ?? 0;
        $newProgress = $progress['%'] ?? 0;

        $oldPhase = $this->getAttribute('phase') ?? 0;
        $oldStep = $this->getAttribute('step') ?? 0;
        $oldProgress = $this->getAttribute('percents') ?? 0;

        if ($oldPhase !== $newPhase || $oldStep !== $newStep || $oldProgress !== $newProgress) {
            // update worker state
            $this->setAttribute('phase', $newPhase);
            $this->setAttribute('step', $newStep);
            $this->setAttribute('percents', $newProgress);
            $this->save();

            Log::debug(sprintf('Worker [%s] state changed to from [%s,%s,%s] to [%s,%s,%s]',
                $this->getAttribute('id'),
                $oldPhase, $oldStep, $oldProgress,
                $newPhase, $newStep, $newProgress
            ));

            // fire worker state changed event if changed
            WorkerStateEvent::dispatch(
                $this->getAttribute('job_id'),
                $oldPhase, $newPhase,
                $oldStep, $newStep,
                $oldProgress, $newProgress
            );
        }
    }
}
