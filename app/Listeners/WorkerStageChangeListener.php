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
use App\Events\Worker\JobDoneEvent;
use App\Events\Worker\PlottingFinishedEvent;
use App\Events\Worker\PlottingStartedEvent;
use App\Events\Worker\PostProcessFinishedEvent;
use App\Events\Worker\PostProcessStartedEvent;
use App\Events\Worker\PreProcessFinishedEvent;
use App\Events\Worker\PreProcessStartedEvent;
use App\Events\Worker\WorkerDoneEvent;
use App\Models\Event;
use Illuminate\Support\Facades\Log;

class WorkerStageChangeListener extends WorkerBaseListener
{
    /**
     * Event processing stub.
     *
     * @param Event $event
     * @param WorkerEventInterface $workerEvent
     *
     * @return  void
     */
    protected function processEvent(Event $event, WorkerEventInterface $workerEvent): void
    {
        $stage = match (get_class($workerEvent)) {
            PreProcessStartedEvent::class => 'WBS',
            PreProcessFinishedEvent::class => 'WBE',
            PlottingStartedEvent::class => 'WPS',
            PlottingFinishedEvent::class => 'WPE',
            PostProcessStartedEvent::class => 'WAS',
            PostProcessFinishedEvent::class => 'WAE',
            WorkerDoneEvent::class => 'WE',
            JobDoneEvent::class => 'JE',
            default => null,
        };

        Log::debug(sprintf('Checking event [%s] is [%s]', $event->name(), $stage));

        if ($stage !== null && $event->isStage($stage)) {
            Log::debug(sprintf('Emitting event [%s]', $event->name()));
            $event->writeEventRecord();
        }
    }
}
