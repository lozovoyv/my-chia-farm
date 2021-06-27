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

use App\Events\ReplottingEvent;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReplottingListener
{
    /**
     * Handle the replotting event.
     *
     * @param ReplottingEvent $event
     *
     * @return  void
     */
    public function handle(ReplottingEvent $event): void
    {
        /** @var Job $job */
        $job = Job::query()->where('id', $event->jobId)->first();

        if ($job === null) {
            return;
        }

        $rePlotLimit = $job->getRePlotLimit();

        if ($rePlotLimit === null) {
            return;
        }

        Log::debug(sprintf('Checking for replotting in [%s] with limit [%s]', $event->destination, $rePlotLimit->format('Y:m:d H:i:s')));

        $this->removeOldest($event->destination, $rePlotLimit);
    }

    /**
     * Remove oldest file in path with ts limit.
     *
     * @param string $path
     * @param Carbon $limit
     *
     * @return  void
     */
    protected function removeOldest(string $path, Carbon $limit): void
    {
        $files = glob(rtrim($path, ' \t\n\r\0\x0B\\/') . DIRECTORY_SEPARATOR . '*.*');

        $files = array_diff($files, ['.', '..']);

        $name = null;
        $ts = null;

        foreach ($files as $file) {
            $ctime = filectime($file);
            if ($ts === null || $ctime < $ts) {
                $name = $file;
                $ts = $ctime;
            }
        }

        if ($name !== null && Carbon::parse($ts) <= $limit) {
            Log::debug(sprintf('Unlinking [%s]', $name));
            unlink($name);
        }
    }
}
