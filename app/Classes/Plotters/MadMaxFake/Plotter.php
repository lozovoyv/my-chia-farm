<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters\MadMaxFake;

use App\Classes\Plotters\MadMax\Plotter as MadMaxPlotter;
use App\Exceptions\PlotterException;

class Plotter extends MadMaxPlotter
{
    /** @var string Name of plotting command. */
    protected static string $name = 'MadMax fake plotter (9e649ae)';

    /**
     * Make command to run plotting.
     *
     * @return  string
     *
     * @throws  PlotterException
     */
    protected function makeCommand(): string
    {
        // make all required dirs and validate attributes
        parent::makeCommand();

        $path = app()->basePath() . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR;
        $fake = __DIR__ . DIRECTORY_SEPARATOR . 'log.txt';

        return "{$path}fake.sh $fake 1";
    }
}
