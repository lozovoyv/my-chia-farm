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

use Carbon\Carbon;
use Illuminate\Foundation\Events\Dispatchable;

class JobEvent
{
    use Dispatchable;

    /** @var string Event name */
    public string $jobEventName;

    /** @var Carbon Time of event firing */
    public Carbon $fireAt;

    /** @var Carbon Time of event created */
    public Carbon $createdAt;

    /**
     * Create a job event instance.
     *
     * @return void
     */
    public function __construct(string $jobEventName, $fireAt, $createdAt)
    {
        $this->jobEventName = $jobEventName;
        $this->fireAt = Carbon::parse($fireAt);
        $this->createdAt = Carbon::parse($createdAt);
    }
}
