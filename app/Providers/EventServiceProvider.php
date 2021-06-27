<?php

namespace App\Providers;

use App\Events\JobEvent;
use App\Events\Worker\JobDoneEvent;
use App\Events\Worker\PlottingFinishedEvent;
use App\Events\Worker\PlottingStartedEvent;
use App\Events\Worker\PostProcessFinishedEvent;
use App\Events\Worker\PostProcessStartedEvent;
use App\Events\Worker\PreProcessFinishedEvent;
use App\Events\Worker\PreProcessStartedEvent;
use App\Events\Worker\WorkerDoneEvent;
use App\Events\WorkerStateEvent;
use App\Listeners\JobEventsListener;
use App\Listeners\WorkerFinishedListener;
use App\Listeners\WorkerStageChangeListener;
use App\Listeners\WorkerStateListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // job emitted events to start workers
        JobEvent::class => [JobEventsListener::class],
        // worker plotting process state change
        WorkerStateEvent::class => [WorkerStateListener::class],
        // worker stage change
        PreProcessStartedEvent::class => [WorkerStageChangeListener::class],
        PreProcessFinishedEvent::class => [WorkerStageChangeListener::class],
        PlottingStartedEvent::class => [WorkerStageChangeListener::class],
        PlottingFinishedEvent::class => [WorkerStageChangeListener::class],
        PostProcessStartedEvent::class => [WorkerStageChangeListener::class],
        PostProcessFinishedEvent::class => [WorkerStageChangeListener::class],
        WorkerDoneEvent::class => [WorkerFinishedListener::class, WorkerStageChangeListener::class],
        JobDoneEvent::class => [WorkerStageChangeListener::class],
    ];
}
