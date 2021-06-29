<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Events;

use App\Events\Helpers\WorkerEventInterface;
use App\Events\Helpers\WorkerEventTrait;
use Illuminate\Foundation\Events\Dispatchable;

class WorkerStateEvent implements WorkerEventInterface
{
    use Dispatchable,
        WorkerEventTrait;

    public int $jobId;
    public int $oldPhase;
    public int $newPhase;
    public int $oldStep;
    public int $newStep;
    public int $oldProgress;
    public int $newProgress;

    /**
     * Create a job event instance.
     *
     * @return void
     */
    public function __construct(int $jobId, int $oldPhase, int $newPhase, int $oldStep, int $newStep, int $oldProgress, int $newProgress)
    {
        $this->jobId = $jobId;
        $this->oldPhase = $oldPhase;
        $this->newPhase = $newPhase;
        $this->oldStep = $oldStep;
        $this->newStep = $newStep;
        $this->oldProgress = $oldProgress;
        $this->newProgress = $newProgress;
    }

    public function jobId(): ?int
    {
        return $this->jobId;
    }
}
