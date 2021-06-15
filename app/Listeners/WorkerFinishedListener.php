<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Listeners;

use App\Events\WorkerFinishedEvent;
use App\Models\Job;

class WorkerFinishedListener
{
    /**
     * Handle the worker finished event.
     *
     * @param WorkerFinishedEvent $event
     *
     * @return  void
     */
    public function handle(WorkerFinishedEvent $event): void
    {
        /** @var Job $job */
        $job = Job::query()->where('id', $event->worker->getAttribute('job_id'))->first();
        if ($job === null) return;

        $job->setAttribute('plots_done', $job->getAttribute('plots_done') + 1);
        $job->save();
    }
}
