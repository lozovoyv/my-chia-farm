<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes;

use loophp\phposinfo\OsInfo;

class OS
{
    /** @var string OS cache */
    protected static string $os;

    /**
     * Get currently running OS.
     *
     * @return  string
     */
    public static function current(): string
    {
        if (!isset(static::$os)) {
            if (OsInfo::isWindows()) {
                static::$os = 'win';
            } else if (OsInfo::isApple()) {
                static::$os = 'mac';
            } else if (OsInfo::isUnix()) {
                static::$os = 'linux';
            } else {
                static::$os = 'unsupported';
            }
        }

        return static::$os;
    }

    /**
     * Resolve array keyed by os name.
     *
     * @param array $entries
     * @param null $default
     *
     * @return  mixed
     */
    public static function resolve(array $entries, $default = null): mixed
    {
        return $entries[static::current()] ?? $default;
    }
}
