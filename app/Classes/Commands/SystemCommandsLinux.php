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

class SystemCommandsLinux implements SystemCommands
{
    /**
     * Get chia keys from installation.
     *
     * @return  int
     */
    public function getCPUCount(): int
    {
        $command = "grep -c ^processor /proc/cpuinfo";

        return shell_exec($command);
    }

    /**
     * Check if process is running.
     *
     * @param int $pid
     *
     * @return  bool
     */
    public function isProcessRunning(int $pid): bool
    {
        $out = shell_exec("ps -p $pid | awk '/$pid/{print $1}'");

        return (int)$out === $pid;
    }

    /**
     * Kill process by PID.
     *
     * @param int $pid
     *
     * @return  void
     */
    public function killProcess(int $pid): void
    {
        shell_exec("kill $pid");
    }
}
