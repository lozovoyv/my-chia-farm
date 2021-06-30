<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers\Pages;

use App\Classes\MCFConfig;
use App\Classes\Plotters\Mapper;
use App\Classes\Plotters\PlotterInterface;
use App\Exceptions\PlotterException;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Traits\SystemCommandsTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Inertia\Inertia;
use Inertia\Response;

class JobsPageController extends Controller
{
    use SystemCommandsTrait;

    /**
     * Get jobs page.
     *
     * @return  Response
     *
     * @throws  BindingResolutionException
     * @throws  PlotterException
     */
    public function page(): Response
    {
        $now = Carbon::now();

        return Inertia::render('Jobs', [
            'cpu_count' => $this->systemCommands()->getCPUCount(),
            'jobs_original' => $this->getJobs(),
            'plotters' => $this->getPlottersWithDefaults(),
            'now' => $now->toDateTimeString(),
        ]);
    }

    /**
     * Get jobs list.
     *
     * @return  array
     */
    protected function getJobs(): array
    {
        return Job::query()->with(['events', 'starts'])->withCount(['workers'])->get()->toArray();
    }

    /**
     * Get plotters names and defaults for all registered plotters.
     *
     * @return  array
     *
     * @throws  PlotterException
     */
    protected function getPlottersWithDefaults(): array
    {
        /** @var \App\Classes\MCFConfig $config */
        $config = $this->app->make(MCFConfig::class);

        $plotters = [];

        foreach (Mapper::iterator() as $alias => $plotter) {
            /** @var PlotterInterface $plotter */

            // get list of arguments for plotter
            $arguments = $plotter::getArgumentsList();

            // get associations for plotter arguments with global defaults
            $associations = $plotter::getGlobalDefaultsAssociations();

            // fill arguments with defaults
            foreach ($arguments as $key => &$value) {
                // try to get default for plotter
                $default = $config->get("plotting.plotters.$alias.arguments.$key");
                // for null case try to get global default value
                if ($default === null && array_key_exists($key, $associations)) {
                    $default = $config->get($associations[$key]);
                }
                $value['default'] = $default;
            }
            unset($value);

            $plotters[$alias] = [
                'name' => $plotter::name(),
                'arguments' => $arguments,
                'executable' => $config->get("plotting.plotters.$alias.executable"),
                'events' => $plotter::getPlotterEvents(),
            ];
        }

        return $plotters;
    }
}
