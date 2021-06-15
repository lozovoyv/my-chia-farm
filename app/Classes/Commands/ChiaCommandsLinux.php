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

use App\Classes\MCFConfig;
use App\Exceptions\ChiaCommandException;

class ChiaCommandsLinux implements ChiaCommands
{
    protected MCFConfig $config;

    /**
     * ChiaCommandsLinux constructor.
     *
     * @param MCFConfig $config
     */
    public function __construct(MCFConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Get chia keys from installation.
     *
     * @param string $installationPath
     *
     * @return  array|null
     */
    public function getKeys(string $installationPath): ?array
    {
        $command = "cd $installationPath && . ./activate && chia keys show";

        $output = shell_exec($command);

        if (empty($output)) {
            return null;
        }

        $output = explode("\n", $output);

        if (count($output) === 1) {
            return null;
        }

        $farmer_key = null;
        $pool_key = null;

        foreach ($output as $line) {
            if (str_starts_with($line, 'Farmer public key')) {
                $params = explode(':', $line);
                $farmer_key = trim($params[1] ?? '');
            } else if (str_starts_with($line, 'Pool public key')) {
                $params = explode(':', $line);
                $pool_key = trim($params[1] ?? '');
            }
        }

        return [
            'farmer_key' => $farmer_key,
            'pool_key' => $pool_key,
        ];
    }

    /**
     * Run command for plotting.
     *
     * @param string $f
     * @param string $p
     * @param int|null $k
     * @param int|null $u
     * @param int|null $b
     * @param int|null $r
     * @param string $t
     * @param string|null $t2
     * @param string $d
     * @param bool $e
     * @param bool $x
     * @param bool $affinity
     * @param array|null $cpus
     * @param string $log
     *
     * @return  int
     *
     * @throws  ChiaCommandException
     */
    public function makePlot(string $f, string $p, ?int $k, ?int $u, ?int $b, ?int $r, string $t, ?string $t2, string $d, bool $e, bool $x, bool $affinity, ?array $cpus, string $log): int
    {
        $installationPath = $this->config->get('general.chia_path');

        if (empty($f) || empty($p)) {
            throw new ChiaCommandException("Farmer or pool public key is empty.");
        }

        if (empty($t) || !is_dir($t)) {
            throw new ChiaCommandException("Temporary directory [$t] is not set or not exists.");
        }

        if (empty($d) || !is_dir($d)) {
            throw new ChiaCommandException("Destination directory [$d] is not set or not exists.");
        }

        if (!empty($t2) && !is_dir($t2)) {
            throw new ChiaCommandException("Secondary temporary directory [$t2] is set, but not exists.");
        }

        if (empty($log)) {
            throw new ChiaCommandException("Log filename is empty.");
        }


        // Make chia plot command
        $command = "chia plots create -f $f -p $p -t $t -d $d";
        $command .= $k ? " -k $k" : null;
        $command .= $u ? " -u $u" : null;
        $command .= $b ? " -b $b" : null;
        $command .= $r ? " -r $r" : null;
        $command .= $t2 ? " -2 $t2" : null;
        $command .= $e ? " -e" : null;
        $command .= $x ? " -x" : null;

        // Process faking for testing purposes
        // $path = app()->basePath() . DIRECTORY_SEPARATOR;
        // $command = "{$path}simulate.sh {$path}simulate.log 0.1";

        // Make taskset command if applicable
        if ($affinity && !empty($cpus)) {
            $command = 'taskset -c ' . implode(',', $cpus) . " $command";
        }

        // Unlink log file if it exists. It should not!
        if (file_exists($log)) {
            unlink($log);
        }

        // Wrap command with nohup
        $command = "nohup sh -c 'cd $installationPath && . ./activate &&  $command' >> $log 2>&1 & printf \"%u\" $!";

        // Run command and get PID
        $pid = shell_exec($command);

        if ((int)$pid === 0) {
            throw new ChiaCommandException("Error running process. $pid");
        }

        return $pid;
    }
}
