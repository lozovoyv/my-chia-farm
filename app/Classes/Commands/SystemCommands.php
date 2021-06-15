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
}
