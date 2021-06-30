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
use App\Jobs\UpdateWorkersJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class CronRun extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'cron:run';

    /** @var string The console command description. */
    protected $description = 'Run processing tasks by cron';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // perform updating every 10 seconds for 6 times
        // because cron event fired only once in 1 minute.
        for ($i = 0; $i < 6; $i++) {

            $started = microtime(true);

            try{
            DispatchEventsJob::dispatch();
            UpdateWorkersJob::dispatch();
            } catch (Exception $e) {
                Log::error(sprintf('Cron task error: %s', $e->getMessage()));
            }

            if ($i < 5) {

                $finished = microtime(true);

                // adjust sleep to make 10 seconds interval
                sleep(10 - ($finished - $started));
            }
        }

        return 0;
    }
}
