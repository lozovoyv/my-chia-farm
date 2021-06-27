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

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\KeyGenerateCommand;

class AppInit extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'init';

    /** @var string The console command description. */
    protected $description = 'Run application initialization.';

    /**
     * Execute the console command.
     *
     * @return  int
     */
    public function handle(): int
    {
        $this->info("\nInitializing application...\n");

        $basePath = $this->laravel->basePath();

        // Copy env file
        if (!file_exists($basePath . DIRECTORY_SEPARATOR . '.env')) {
            copy($basePath . DIRECTORY_SEPARATOR . '.env.example', $basePath . DIRECTORY_SEPARATOR . '.env');
        }

        // Check APP_KEY
        if (empty($this->laravel['config']['app.key'])) {
            $this->call(KeyGenerateCommand::class);
        }

        if ($this->call(DBInit::class) !== 0) {

            $this->error('Problems with setting up database. Fix it and try again.');

            return 1;
        }

        if ($this->call(UserAdd::class) !== 0) {

            $this->error('Error creating new user. Fix problems and try again.');

            return 1;
        }

        // Print instructions
        $php = PHP_BINARY;
        $this->info("\nApplication is nearly ready to run. Several things left to do:\n");
        $this->info("1. Check (and fix) your timezone in `$basePath/.env` file (TZ=%your_time_zone%)\n");
        $this->info("2. Setup your job scheduler (typically cron) to run `php artisan cron:run` command every minute");
        $this->info('   For cron you need run `crontab -e`');
        $this->info("   and add following: `* * * * * cd $basePath && $php artisan cron:run > /dev/null 2>&1`\n");
        $this->info("3. Run `php artisan serve` command to run application on localhost or setup apache/nginx web server to have access through internet\n");
        $this->info("4. Open your browser and type `http://localhost:8000` (port is typically 8000, but it can vary, see output of previous command)");

        return 0;
    }
}
