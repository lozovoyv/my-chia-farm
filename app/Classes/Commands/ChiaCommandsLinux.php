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

class ChiaCommandsLinux implements ChiaCommands
{
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
}
