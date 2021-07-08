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
use Inertia\Inertia;
use Inertia\Response;

class SettingsPageController extends Controller
{
    /**
     * Get settings page.
     *
     * @return  Response
     * @throws  PlotterException
     */
    public function page(): Response
    {
        /** @var \App\Classes\MCFConfig $config */
        $config = $this->app->make(MCFConfig::class);

        $generalAttributes = $this->generalPlottersAttributes();
        $plottersAttributes = $this->plottersAttributes();

        return Inertia::render('Settings', [
            // all configs
            'config' => $config->get(),
            // plotters settings
            'plotting_general' => $generalAttributes,
            'plotting_plotters' => $plottersAttributes,
        ]);
    }

    /**
     * Get arguments list for registered plotters.
     *
     * @return  array
     *
     * @throws  PlotterException
     */
    protected function plottersAttributes(): array
    {
        $arguments = [];

        foreach (Mapper::iterator() as $alias => $plotter) {
            /** @var PlotterInterface $plotter */
            $args = $plotter::getArgumentsList();
            // unset required for defaults
            foreach ($args as &$arg) {
                $arg['required'] = false;
            }
            unset($arg);
            $arguments[$alias] = [
                'name' => $plotter::name(),
                'executable' => ['type' => 'string', 'required' => true, 'title' => 'Plotter executable'],
                'arguments' => $args,
            ];
        }

        return $arguments;
    }

    /**
     * Get arguments list for general section.
     *
     * @return  array
     */
    protected function generalPlottersAttributes(): array
    {
        return [
            'farmer_key' => ['type' => 'string', 'required' => false, 'title' => 'Hex farmer public key'],
            'pool_key' => ['type' => 'string', 'required' => false, 'title' => 'Hex public key of pool'],
            'pool_contract' => ['type' => 'string', 'required' => false, 'title' => 'Pool contract address'],
            'temp_dir' => ['type' => 'string', 'required' => false, 'title' => 'Temporary directory for plotting files'],
            'temp2_dir' => ['type' => 'string', 'required' => false, 'title' => 'Second temporary directory for plotting files'],
            'dest_dir' => ['type' => 'string', 'required' => false, 'title' => 'Final directory for plots'],
        ];
    }
}
