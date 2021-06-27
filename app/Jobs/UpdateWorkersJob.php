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

use App\Exceptions\PlotterException;
use App\Exceptions\SystemCommandException;
use App\Models\Worker;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateWorkersJob implements ShouldQueue
{
    use Dispatchable;

    /**
     * Perform status updating for all existing workers.
     *
     * @return  void
     */
    public function handle(): void
    {
        $workers = Worker::all();

        foreach ($workers as $worker) {
            /** @var Worker $worker */
            try {
                $worker->updateState();
            } catch (PlotterException | BindingResolutionException | SystemCommandException $e) {
                Log::error(sprintf('[%s] Error updating state of workers [%s]: %s',
                    static::class,
                    $worker->getAttribute('id'),
                    $e->getMessage()
                ));
            }
        }
    }
}
