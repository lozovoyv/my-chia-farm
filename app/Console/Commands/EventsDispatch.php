<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Console\Commands;

use App\Jobs\DispatchEventsJob;
use Illuminate\Console\Command;

class EventsDispatch extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'events:dispatch';

    /** @var string The console command description. */
    protected $description = 'Dispatch job events';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        DispatchEventsJob::dispatch();

        return 0;
    }
}
