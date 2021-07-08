<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters\MadMax;

use App\Classes\Plotters\BasePlotter;
use App\Classes\Plotters\ParserInterface;
use App\Exceptions\PlotterException;

class Plotter extends BasePlotter
{
    /** @var string Name of plotting command. */
    protected static string $name = 'MadMax plotter';

    /** @var array Arguments list applicable to this plotting command. */
    protected static array $argumentsList = [
        '-f' => ['type' => 'string', 'required' => true, 'title' => 'Hex farmer public key'],
        '-c' => ['type' => 'string', 'required' => false, 'title' => 'Pool contract address'],
        '-p' => ['type' => 'string', 'required' => false, 'title' => 'Hex public key of pool'],
        '-t' => ['type' => 'string', 'required' => true, 'title' => 'Temporary directory, needs ~220 GiB'],
        '-2' => ['type' => 'string', 'required' => false, 'title' => 'Temporary directory 2, needs ~110 GiB [RAM] (default = -t)'],
        '-d' => ['type' => 'string', 'required' => true, 'title' => 'Final directory'],
        '-r' => ['type' => 'int', 'required' => false, 'title' => 'Number of threads'],
        '-u' => ['type' => 'int', 'required' => false, 'title' => 'Number of buckets'],
        '-v' => ['type' => 'int', 'required' => false, 'title' => 'Number of buckets for phase 3+4 (default = -b)'],
        '-G' => ['type' => 'bool', 'required' => false, 'title' => 'Alternate tmpdir/tmpdir2'],
    ];

    /** @var array Arguments defaults applicable to this plotting command. */
    protected static array $argumentsDefaults = [
        '-f' => null,
        '-c' => null,
        '-p' => null,
        '-t' => null,
        '-2' => null,
        '-d' => null,
        '-r' => 4,
        '-u' => 256,
        '-v' => 256,
        '-G' => false,
    ];

    /** @var array|string[] Keys association with global defaults */
    protected static array $globalDefaultsAssociations = [
        '-f' => 'plotting.globals.farmer_key',
        '-c' => 'plotting.globals.pool_contract',
        '-p' => 'plotting.globals.pool_key',
        '-t' => 'plotting.globals.temp_dir',
        '-2' => 'plotting.globals.temp2_dir',
        '-d' => 'plotting.globals.dest_dir',
    ];

    /** @var array Keys associated with temporary directories to br cleaned after all */
    protected static array $temporaryDirectoriesKeys = [
        '-t',
        '-2',
    ];

    /** @var string Key associated with destination directory */
    protected static string $destinationDirectoryKey = '-d';

    /**
     * Get plotter events list.
     *
     * @return  array
     */
    public static function getPlotterEvents(): array
    {
        return require 'events.php';
    }

    /**
     * Make command to run plotting.
     *
     * @throws  PlotterException
     */
    protected function makeCommand(): string
    {
        $command = $this->executable();

        return $this->fillOptions($command, [
            '-f' => (string)$this->getArgument('-f', true, 'Farmer public key is empty'),
            '-p' => (string)$this->getArgument('-p', !$this->hasArgument('-c'), 'Both pool contract address and pool public key is empty'),
            '-c' => (string)$this->getArgument('-c', !$this->hasArgument('-p'), 'Both pool contract address and pool public key is empty'),
            '-t' => $this->getTempDir('-t', $this->postfix(), true, true, 'Temporary directory is not set.'),
            '-2' => $this->getTempDir('-2', $this->postfix(), true),
            '-d' => $this->getDir('-d', true, 'Destination path is empty'),
            '-r' => (int)$this->getArgument('-r'),
            '-u' => (int)$this->getArgument('-u'),
            '-v' => (int)$this->getArgument('-v'),
            '-G' => (int)$this->getArgument('-G'),
        ]);
    }

    /**
     * Get parser for this plotter.
     *
     * @return  ParserInterface
     *
     * @throws  PlotterException
     */
    public function parser(): ParserInterface
    {
        return new Parser($this->getLogFileName(), $this->arguments());
    }
}
