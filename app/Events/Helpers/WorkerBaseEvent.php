<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Events\Helpers;

use Illuminate\Foundation\Events\Dispatchable;

abstract class WorkerBaseEvent implements WorkerEventInterface
{
    use Dispatchable,
        WorkerEventTrait;

    /** @var int|null Id of job fired this event */
    private ?int $jobId;

    /**
     * Create a worker event instance.
     *
     * @return void
     */
    public function __construct(?int $jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * Get job id.
     *
     * @return  int|null
     *
     * @internal
     */
    protected function jobId():?int
    {
        return $this->jobId;
    }
}
