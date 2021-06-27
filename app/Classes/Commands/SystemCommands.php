<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Commands;

use App\Exceptions\SystemCommandException;

interface SystemCommands
{
    /**
     * Get chia keys from installation.
     *
     * @return  int
     */
    public function getCPUCount(): int;

    /**
     * Check if process with PID is running.
     *
     * @param int $pid
     *
     * @return  bool
     */
    public function isProcessRunning(int $pid): bool;

    /**
     * Kill process by PID.
     *
     * @param int $pid
     *
     * @return  void
     */
    public function killProcess(int $pid): void;

    /**
     * Apply CPU affinity settings to command.
     *
     * @param string $command
     * @param bool $enabled
     * @param array $cores
     *
     * @return  string wrapped command
     */
    public function applyCPUAffinity(string $command, bool $enabled, array $cores): string;

    /**
     * Run command in background and return pid of process.
     *
     * @param string $command
     * @param string|null $output
     *
     * @return  int PID of process
     *
     * @throws  SystemCommandException
     */
    public function runInBackground(string $command, ?string $output): int;
}
