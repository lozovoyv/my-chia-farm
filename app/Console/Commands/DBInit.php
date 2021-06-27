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

class DBInit extends Command
{
    /** @var string The name and signature of the console command. */
    protected $signature = 'db:init';

    /** @var string The console command description. */
    protected $description = 'Run application database initialization';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info("\nInitializing database...\n");

        // write database default path
        $dbPath = $this->laravel['config']['database.connections.sqlite.database'];
        if ($dbPath === null || (
                $this->warn("Database path already set to $dbPath") ||
                $this->confirm('Reset it to default?'))
        ) {
            $dbPath = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'my_chia_farm.sqlite';

            $this->writeNewEnvironmentFileWith(
                'DB_DATABASE',
                'database.connections.sqlite.database',
                $dbPath
            );

            $this->laravel['config']['database.connections.sqlite.database'] = $dbPath;

            $this->info("Database path is set to $dbPath");
        }

        // run database migration
        if (!file_exists($dbPath) && !touch($dbPath)) {
            $this->error('Can\'t create database file.');

            return 1;
        }

        return $this->call('migrate', ['--force' => true]);
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $envKey
     * @param string $configKey
     * @param string $value
     *
     * @return  void
     */
    protected function writeNewEnvironmentFileWith(string $envKey, string $configKey, string $value): void
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern($configKey, $envKey),
            "$envKey=$value",
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env KEY with any random key.
     *
     * @param string $configKey
     * @param string $envKey
     *
     * @return  string
     */
    protected function keyReplacementPattern(string $configKey, string $envKey): string
    {
        $escaped = preg_quote('=' . $this->laravel['config'][$configKey], '/');

        return "/^$envKey$escaped/m";
    }
}
