<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Classes\MCFConfig;
use App\Models\Event;
use App\Models\Job;
use App\Traits\SystemCommandsTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Inertia\Inertia;
use Inertia\Response;

class PagesController extends Controller
{
    use SystemCommandsTrait;

    /**
     * Get dashboard page.
     *
     * @return  Response
     */
    public function homePage(): Response
    {
        $jobs = Job::query()->with(['events', 'starts', 'workers'])->get();

        return Inertia::render('Dashboard', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Get jobs page.
     *
     * @return  Response
     *
     * @throws  BindingResolutionException
     */
    public function jobsPage(): Response
    {
        $app = app();

        /** @var MCFConfig $config */
        $config = $app->make(MCFConfig::class);

        $jobs = Job::query()->with(['events', 'starts'])->get();

        $eventNames = Event::query()->pluck('name')->unique();

        return Inertia::render('Jobs', [
            'm_config' => $config->get(),
            'cpu_count' => $this->systemCommands()->getCPUCount(),
            'jobs_original' => $jobs->toArray(),
            'event_names_original' => $eventNames,
        ]);
    }

    /**
     * Get settings page.
     *
     * @return  Response
     *
     * @throws  BindingResolutionException
     */
    public function settingsPage(): Response
    {
        $app = app();

        /** @var \App\Classes\MCFConfig $config */
        $config = $app->make(MCFConfig::class);

        return Inertia::render('Settings', [
            'm_config' => $config->get(),
        ]);
    }
}
