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

use Illuminate\Foundation\Events\Dispatchable;

class ReplottingEvent
{
    use Dispatchable;

    /** @var int|null Id of job fired this event */
    public ?int $jobId;

    /** @var string Destination path */
    public string $destination;

    /**
     * Create a replotting event instance.
     *
     * @return void
     */
    public function __construct(?int $jobId, string $destination)
    {
        $this->jobId = $jobId;
        $this->destination = $destination;
    }
}
