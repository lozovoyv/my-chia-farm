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

use App\Events\JobEvent;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchEventsJob implements ShouldQueue
{
    use Dispatchable;

    /**
     * Perform job event firing.
     *
     * @return  void
     */
    public function handle(): void
    {
        $now = Carbon::now();

        $events = DB::table('mp_job_events')->where('fire_at', '<=', $now)->get();
        DB::table('mp_job_events')->where('fire_at', '<=', $now)->delete();

        foreach ($events as $event) {
            Log::debug("Processing event $event->job_event_name");
            JobEvent::dispatch($event->job_event_name, $event->fire_at, $event->created_at);
        }
    }
}
