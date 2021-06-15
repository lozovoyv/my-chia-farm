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

use App\Exceptions\ChiaCommandException;
use App\Models\Worker;

interface ChiaCommands
{
    /**
     * Get chia keys from installation.
     *
     * @param string $installationPath
     *
     * @return  array|null
     */
    public function getKeys(string $installationPath): ?array;

    /**
     * Run command for plotting.
     *
     * @param string $f
     * @param string $p
     * @param int $k
     * @param int $u
     * @param int $b
     * @param int $r
     * @param string $t
     * @param string $t2
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
    public function makePlot(string $f, string $p, int $k, int $u, int $b, int $r, string $t, string $t2, string $d, bool $e, bool $x, bool $affinity, ?array $cpus, string $log): int;
}
