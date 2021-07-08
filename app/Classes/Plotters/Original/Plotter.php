<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes\Plotters\Original;

use App\Classes\Plotters\BasePlotter;
use App\Classes\Plotters\ParserInterface;
use App\Exceptions\PlotterException;

class Plotter extends BasePlotter
{
    /** @var string Name of plotting command. */
    protected static string $name = 'Original chia plotter';

    /** @var array Arguments list applicable to this plotting command. */
    protected static array $argumentsList = [
        '-f' => ['type' => 'string', 'required' => true, 'title' => 'Hex farmer public key'],
        '-c' => ['type' => 'string', 'required' => false, 'title' => 'Pool contract address'],
        '-p' => ['type' => 'string', 'required' => false, 'title' => 'Hex public key of pool'],
        '-t' => ['type' => 'string', 'required' => true, 'title' => 'Temporary directory for plotting files'],
        '-2' => ['type' => 'string', 'required' => false, 'title' => 'Second temporary directory for plotting files'],
        '-d' => ['type' => 'string', 'required' => true, 'title' => 'Final directory for plots'],
        '-k' => ['type' => 'select', 'required' => false, 'title' => 'Plot size', 'options' => [32, 33, 34, 35]],
        '-u' => ['type' => 'select', 'required' => false, 'title' => 'Number of buckets', 'options' => [32, 64, 128]],
        '-b' => ['type' => 'int', 'required' => false, 'title' => 'Megabytes for sort/plot buffer'],
        '-r' => ['type' => 'int', 'required' => false, 'title' => 'Number of threads to use'],
        '-e' => ['type' => 'bool', 'required' => false, 'title' => 'Disable bitfield'],
        '-x' => ['type' => 'bool', 'required' => false, 'title' => 'Skips adding [final dir] to harvester for farming'],
    ];

    /** @var array Arguments defaults applicable to this plotting command. */
    protected static array $argumentsDefaults = [
        '-f' => null,
        '-c' => null,
        '-p' => null,
        '-t' => null,
        '-2' => null,
        '-d' => null,
        '-k' => 32,
        '-u' => 128,
        '-b' => 3389,
        '-r' => 2,
        '-e' => false,
        '-x' => true,
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
     * Make command to run plotting. This function also must validate all required attributes and throws exception on
     * validation fait.
     * All temporary directories must me creates by getTempDir() function with postfix to ensure it will properly
     * cleaned up after process finished.
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
            '-k' => (int)$this->getArgument('-k'),
            '-u' => (int)$this->getArgument('-u'),
            '-b' => (int)$this->getArgument('-b'),
            '-r' => (int)$this->getArgument('-r'),
            '-e' => (bool)$this->getArgument('-e'),
            '-x' => (bool)$this->getArgument('-x'),
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
