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
use Illuminate\Support\Facades\Log;

class SystemCommandsLinux implements SystemCommands
{
    /**
     * Get cores count.
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
        $command = "ps -p $pid | awk '/$pid/{print $1}'";

        $out = shell_exec($command);

        return (int)$out === $pid;
    }

    /**
     * Kill process by PID and all its children.
     *
     * @param int $pid
     *
     * @return  void
     */
    public function killProcess(int $pid): void
    {
        $PIDs = [$pid];

        while (!empty($child = shell_exec("pgrep -P $PIDs[0]"))) {
            array_unshift($PIDs, $child);
        }

        foreach ($PIDs as $process) {
            if ($this->isProcessRunning($process)) {
                shell_exec("kill $process");
            }
        }
    }

    /**
     * Apply CPU affinity settings to command.
     *
     * @param string $command
     * @param bool $enabled
     * @param array $cores
     *
     * @return  string
     */
    public function applyCPUAffinity(string $command, bool $enabled, array $cores): string
    {
        if (!$enabled || empty($cores)) {
            return $command;
        }

        $command = str_replace('"', '\"', $command);

        return 'taskset -c ' . implode(',', $cores) . " sh -c \"$command\"";
    }

    /**
     * Run command in background and return pid of process.
     *
     * @param string $command
     * @param string|null $output
     *
     * @return  int
     *
     * @throws  SystemCommandException
     */
    public function runInBackground(string $command, ?string $output): int
    {
        if (empty($output)) {
            $output = '/dev/null';
        }

        $command = str_replace(['\"', '"'], '\"', $command);
        $command = "nohup sh -c \"$command\" >> $output 2>&1 </dev/null & printf \"%u\" $!";

        Log::debug(sprintf('Running command: %s', $command));

        $pid = shell_exec($command);

        Log::debug(sprintf('Command output: %s', $pid));

        if ((int)$pid === 0) {
            throw new SystemCommandException("Error running process for [$command]");
        }

        return $pid;
    }
}
