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
use App\Classes\LogParser;
use App\Classes\MCFConfig;
use App\Classes\OriginalLogParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return  void
     */
    public function register()
    {
        // Register My Chia Farm config instance
        $filename = $this->app->storagePath() . DIRECTORY_SEPARATOR . 'maxplot.yaml';
        $config = new MCFConfig($filename);
        $this->app->instance(MCFConfig::class, $config);

        // Bind commands
        // TODO: add another OSs
        $this->app->bind(ChiaCommands::class, ChiaCommandsLinux::class);
        $this->app->bind(SystemCommands::class, SystemCommandsLinux::class);

        // Bind log parser
        $this->app->bind(LogParser::class, OriginalLogParser::class);
    }
}
