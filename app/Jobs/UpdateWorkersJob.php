<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Jobs;

use App\Models\Worker;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateWorkersJob implements ShouldQueue
{
    use Dispatchable;

    /**
     * Perform status updating for all existing workers.
     *
     * @return  void
     *
     * @throws  BindingResolutionException
     */
    public function handle()
    {
        $workers = Worker::all();

        foreach ($workers as $worker) {
            /** @var Worker $worker */
            $worker->updateState();
        }
    }
}
