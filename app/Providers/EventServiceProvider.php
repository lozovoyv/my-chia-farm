<?php

namespace App\Providers;

use App\Events\JobEvent;
use App\Events\WorkerFinishedEvent;
use App\Events\WorkerStateEvent;
use App\Listeners\JobEventsListener;
use App\Listeners\WorkerFinishedListener;
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
        JobEvent::class => [
            JobEventsListener::class,
        ],
        WorkerStateEvent::class => [
            WorkerStateListener::class
        ],
        WorkerFinishedEvent::class => [
            WorkerFinishedListener::class,
        ]
    ];
}
