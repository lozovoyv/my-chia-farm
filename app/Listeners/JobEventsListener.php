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

use App\Events\JobEvent;
use App\Models\Job;
use App\Models\Start;
use Illuminate\Contracts\Container\BindingResolutionException;

class JobEventsListener
{
    /**
     * Handle the job event.
     *
     * @param JobEvent $event
     *
     * @return  void
     *
     * @throws  BindingResolutionException
     */
    public function handle(JobEvent $event): void
    {
        $starts = Start::query()->where('event_name', $event->jobEventName)->with(['job', 'job.workers'])->get();

        foreach ($starts as $start) {
            /** @var Start $start */
            /** @var Job $job */
            $job = $start->getRelation('job');

            // get number of workers can be started (with disabled condition)
            $numberOfWorkersCanBeStarted = $job->numberOfWorkersCanStart(
                $start->numberOfWorkersToStart()
            );

            // and start as many workers of desired as job can
            $job->start($numberOfWorkersCanBeStarted);
        }
    }
}
