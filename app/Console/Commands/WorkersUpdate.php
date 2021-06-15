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

use App\Jobs\UpdateWorkersJob;
use Illuminate\Console\Command;

class WorkersUpdate extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'workers:update';

    /** @var string The console command description. */
    protected $description = 'Update status for all running workers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        UpdateWorkersJob::dispatch();

        return 0;
    }
}
