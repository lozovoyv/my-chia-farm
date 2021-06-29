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

use App\Events\Helpers\WorkerEventInterface;
use App\Events\WorkerStateEvent;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class WorkerStateListener extends WorkerBaseListener
{
    /**
     * Process worker state changed event.
     *
     * @param Event $event
     * @param WorkerEventInterface $workerEvent
     *
     * @return  void
     */
    protected function processEvent(Event $event, WorkerEventInterface $workerEvent): void
    {
        /** @var WorkerStateEvent $workerEvent */

        Log::debug(sprintf('Checking job [%s] event [%s] to be fired on state change', $workerEvent->jobId, $event->name()));

        if ($event->isTimeToFire($workerEvent)) {

            $event->writeEventRecord();

            Log::debug(sprintf('Emitting event [%s]', $event->getAttribute('name')));
        }
    }
}
