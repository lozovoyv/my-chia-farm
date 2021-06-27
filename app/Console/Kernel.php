<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return  void
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
