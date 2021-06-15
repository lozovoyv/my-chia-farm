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

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EventsAdd extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'events:add {name? : The event name} {--time= : Time to fire event}';

    /** @var string The console command description. */
    protected $description = 'Add job event to queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if (($name = $this->argument('name')) !== null) {
            $time = $this->hasOption('time') ? $this->option('time') : 'now';
        } else {
            $name = $this->ask('Enter job event name');
            $time = $this->ask('Enter time to fire');
        }

        DB::table('mp_job_events')->insert([
            'job_event_name' => $name,
            'fire_at' => Carbon::parse($time),
            'created_at' => Carbon::now(),
        ]);

        $this->info('Event added');

        return 0;
    }
}
