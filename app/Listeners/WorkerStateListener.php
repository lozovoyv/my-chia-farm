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

use App\Events\WorkerStateEvent;
use App\Models\Event;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkerStateListener
{
    /**
     * Handle the worker state changed event.
     *
     * @param WorkerStateEvent $changed
     *
     * @return  void
     */
    public function handle(WorkerStateEvent $changed): void
    {
        $job = Job::query()->where('id', $changed->jobId)->with(['events'])->first();
        if ($job === null) return;

        $events = $job->getRelation('events');
        if ($events->count() === 0) return;

        foreach ($events as $event) {
            /** @var Event $event */

            Log::debug(sprintf('Checking job [%s] event [%s] to be fired', $job->getAttribute('id'), $event->getAttribute('name')));

            if ($event->isTimeToFire($changed)) {
                DB::table('mp_job_events')->insert($event->makeEventRecord());
                Log::debug(sprintf('Emitting event [%s]', $event->getAttribute('name')));
            }
        }
    }
}
