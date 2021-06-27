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

use App\Exceptions\PlotterException;
use App\Exceptions\SystemCommandException;
use Illuminate\Contracts\Container\BindingResolutionException;

interface PlotterInterface
{
    /**
     * Plotter constructor.
     *
     * @param string $postfix
     * @param string $executable
     * @param array $arguments
     * @param bool $affinity
     * @param array|null $cpus
     * @param string $log
     */
    public function __construct(string $postfix, string $executable, array $arguments, bool $affinity, ?array $cpus, string $log);

    /**
     * Get name of plotting command.
     *
     * @return  string
     */
    public static function name(): string;

    /**
     * Get arguments list applicable to this plotting command.
     *
     * @return  array
     */
    public static function getArgumentsList(): array;

    /**
     * Get arguments defaults applicable to this plotting command.
     *
     * @return  array
     */
    public static function getArgumentsDefaults(): array;

    /**
     * Get associations of arguments with global defaults.
     *
     * @return  array
     */
    public static function getGlobalDefaultsAssociations(): array;

    /**
     * Get plotter events list.
     *
     * @return  array
     */
    public static function getPlotterEvents(): array;

    /**
     * Clean up temporary directories and optionally log file.
     *
     * @throws  PlotterException
     */
    public function cleanUp(bool $saveLog = false): void;

    /**
     * Test plotting command for proper conditions.
     *
     * @throws  PlotterException
     */
    public function test(): void;

    /**
     * Run plotting command.
     *
     * @return  int PID of running process
     *
     * @throws  PlotterException
     * @throws  BindingResolutionException
     * @throws  SystemCommandException
     */
    public function run(): int;

    /**
     * Get destination directory for complete plots.
     *
     * @return  string|null
     *
     * @throws  PlotterException
     */
    public function getDestination(): ?string;

    /**
     * Get log parser for plotter.
     *
     * @return  ParserInterface
     *
     * @throws  PlotterException
     */
    public function parser(): ParserInterface;
}
