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

use App\Events\Worker\WorkerDoneEvent;

class WorkerFinishedListener
{
    /**
     * Handle the worker finished event.
     *
     * @param WorkerDoneEvent $event
     *
     * @return  void
     */
    public function handle(WorkerDoneEvent $event): void
    {
        if ($event->job() === null) {
            return;
        }

        $event->job()->iterationDone();
    }
}
