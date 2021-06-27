<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Console\Commands;

use App\Classes\MCFConfig;
use App\Classes\Plotters\Mapper;
use App\Classes\Plotters\PlotterInterface;
use App\Exceptions\PlotterException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;

class ConfigInit extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'config:update';

    /** @var string The console command description. */
    protected $description = 'Update or create config.';

    protected array $defaults = [
        'plotting.globals.farmer_key' => null,
        'plotting.globals.pool_key' => null,
        'plotting.globals.temp_dir' => null,
        'plotting.globals.temp2_dir' => null,
        'plotting.globals.dest_dir' => null,
    ];

    /**
     * Execute the console command.
     *
     * @return  int
     *
     * @throws  BindingResolutionException
     * @throws  PlotterException
     */
    public function handle(): int
    {
        /** @var MCFConfig $config */
        $config = $this->laravel->make(MCFConfig::class);

        $pending = [];

        foreach ($this->defaults as $key => $value) {
            if (!$config->has($key)) {
                $pending[$key] = $value;
            }
        }

        foreach (Mapper::iterator() as $alias => $plotter) {
            /** @var PlotterInterface $plotter */
            $defaults = $plotter::getArgumentsDefaults();
            if (!$config->has("plotting.plotters.$alias.executable")) {
                $pending["plotting.plotters.$alias.executable"] = null;
            }
            foreach ($defaults as $key => $value) {
                if (!$config->has("plotting.plotters.$alias.arguments.$key")) {
                    $pending["plotting.plotters.$alias.arguments.$key"] = $value;
                }
            }
        }

        $config->set($pending);

        return 0;
    }
}
