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
}
