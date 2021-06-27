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
use App\Models\Event;

abstract class WorkerBaseListener
{
    /**
     * Handle the worker state changed event.
     *
     * @param WorkerEventInterface $event
     *
     * @return  void
     */
    public function handle(WorkerEventInterface $event): void
    {
        if ($event->events() === null) {
            return;
        }

        foreach ($event->events() as $workerEvent) {
            /** @var Event $workerEvent */
            $this->processEvent($workerEvent, $event);
        }
    }

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
        // Do event processing
    }
}
