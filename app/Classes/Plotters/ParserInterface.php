<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters;

interface ParserInterface
{
    /**
     * Parser constructor.
     *
     * @param string $filename
     * @param array $arguments
     */
    public function __construct(string $filename, array $arguments);

    /**
     * Get progress of plot seeding parsed from log.
     *
     * @return  Progress
     */
    public function getProgress(): Progress;
}
