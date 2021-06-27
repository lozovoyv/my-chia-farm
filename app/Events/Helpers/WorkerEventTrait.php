<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Events\Helpers;

use App\Models\Job;
use Illuminate\Support\Collection;

trait WorkerEventTrait
{
    /** @var Job Resolved job cache */
    private Job $job;

    /** @var Collection Events associated with assigned job cache */
    private Collection $events;

    /** @var bool Is there any events */
    private bool $hasEvents;

    /**
     * Get job related to this event.
     *
     * @return  Job
     */
    public function job(): Job
    {
        if (!isset($this->job)) {
            /** @var Job $job */
            $job = Job::query()->where('id', $this->jobId())->with(['events'])->first();
            $this->job = $job;
        }

        return $this->job;
    }

    /**
     * Get events associated with assigned job.
     *
     * @return  Collection|null
     */
    public function events(): ?Collection
    {
        if (!isset($this->hasEvents)) {
            if ($this->job() === null || !$this->job()->isEventsEnabled()) {
                $this->hasEvents = false;
                return null;
            }
            $this->events = $this->job()->getRelation('events');
            $this->hasEvents = $this->events->count() > 0;
        }

        return $this->hasEvents ? $this->events : null;
    }
}
