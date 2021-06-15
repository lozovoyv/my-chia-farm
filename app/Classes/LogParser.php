<?php
/*
 * This file is part of the MyFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes;

interface LogParser
{
    /**
     * OriginalLogParser constructor.
     *
     * @param string $filename
     * @param int $buckets
     */
    public function __construct(string $filename, int $buckets = 128);

    /**
     * Get progress of plot seeding parsed from log.
     *
     * @return int[]
     */
    public function getProgress(): array;
}
