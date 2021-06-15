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

use App\Models\Worker;
use Illuminate\Foundation\Events\Dispatchable;

class WorkerFinishedEvent
{
    use Dispatchable;

    /** @var Worker */
    public Worker $worker;

    /**
     * Create a job event instance.
     *
     * @return void
     */
    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
    }
}
