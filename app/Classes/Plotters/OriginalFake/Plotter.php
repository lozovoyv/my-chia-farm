<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters\OriginalFake;

use App\Classes\Plotters\Original\Plotter as OriginalPlotter;
use App\Exceptions\PlotterException;

class Plotter extends OriginalPlotter
{
    /** @var string Name of plotting command. */
    protected static string $name = 'Original chia fake plotter';

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

        return "{$path}fake.sh $fake 0.1";
    }
}
