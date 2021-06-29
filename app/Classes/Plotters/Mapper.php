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
use Generator;

class Mapper
{
    protected static array $registered = [
        'original_chia' => Original\Plotter::class,
        'mad_max' => MadMax\Plotter::class,
        // 'original_chia_fake' => OriginalFake\Plotter::class,
        // 'mad_max_fake' => MadMaxFake\Plotter::class,
    ];

    /**
     * Get all registered plotters aliases.
     *
     * @return  array
     */
    public static function registered(): array
    {
        return array_keys(static::$registered);
    }

    /**
     * Get class of alias.
     *
     * @param string $alias
     *
     * @return  string|null
     *
     * @throws  PlotterException
     */
    public static function getClassOf(string $alias): ?string
    {
        if (($class = static::$registered[$alias] ?? null) === null) {
            throw new PlotterException(sprintf('[%s] Can not resolve plotter [%s]', static::class, $alias));
        }

        return $class;
    }

    /**
     * Make alias iterator for all registered.
     *
     * @return  Generator
     *
     * @throws  PlotterException
     */
    public static function iterator(): Generator
    {
        foreach (static::registered() as $alias) {
            yield $alias => static::getClassOf($alias);
        }
    }

    /**
     * Get name of plotter by alias.
     *
     * @param string $alias
     *
     * @return  string
     *
     * @throws  PlotterException
     */
    public static function name(string $alias): string
    {
        /** @var PlotterInterface $class */
        $class = static::getClassOf($alias);
        return $class::name();
    }

    /**
     * Get defaults for plotting command by alias.
     *
     * @param string $alias
     *
     * @return  array
     *
     * @throws  PlotterException
     */
    public static function defaults(string $alias): array
    {
        /** @var PlotterInterface $class */
        $class = static::getClassOf($alias);
        return $class::getArgumentsDefaults();
    }

    /**
     * Get arguments list for plotting command by alias.
     *
     * @param string $alias
     *
     * @return  array
     *
     * @throws  PlotterException
     */
    public static function arguments(string $alias): array
    {
        /** @var PlotterInterface $class */
        $class = static::getClassOf($alias);
        return $class::getArgumentsList();
    }

    /**
     * Create plotter by alias.
     *
     * @param string $alias
     * @param string $postfix
     * @param string $executable
     * @param array $arguments
     * @param bool $affinity
     * @param array|null $cpus
     * @param string $log
     *
     * @return  PlotterInterface
     *
     * @throws  PlotterException
     */
    public static function make(string $alias, string $postfix, string $executable, array $arguments, bool $affinity, ?array $cpus, string $log): PlotterInterface
    {
        $class = static::getClassOf($alias);

        return new $class($postfix, $executable, $arguments, $affinity, $cpus, $log);
    }
}
