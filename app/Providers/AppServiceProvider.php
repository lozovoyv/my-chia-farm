<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Providers;

use App\Classes\Commands\ChiaCommands;
use App\Classes\Commands\SystemCommands;
use App\Classes\Commands\ChiaCommandsLinux;
use App\Classes\Commands\SystemCommandsLinux;
use App\Classes\MCFConfig;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return  void
     */
    public function register(): void
    {
        // Register My Chia Farm config instance
        $filename = $this->app->basePath() . DIRECTORY_SEPARATOR . 'mcf_config.yaml';
        $config = new MCFConfig($filename);
        $this->app->instance(MCFConfig::class, $config);

        // Bind commands
        // TODO: add another OSs
        $this->app->bind(ChiaCommands::class, ChiaCommandsLinux::class);
        $this->app->bind(SystemCommands::class, SystemCommandsLinux::class);
    }
}
