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
use App\Traits\SystemCommandsTrait;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

abstract class BasePlotter implements PlotterInterface
{
    use SystemCommandsTrait;

    /** @var array Arguments list applicable to this plotting command. */
    protected static array $argumentsList;

    /** @var array Arguments defaults applicable to this plotting command. */
    protected static array $argumentsDefaults;

    /** @var string Name of plotting command. */
    protected static string $name;

    /** @var array|string[] Keys association with global defaults */
    protected static array $globalDefaultsAssociations;

    /** @var array Keys associated with temporary directories to br cleaned after all */
    protected static array $temporaryDirectoriesKeys;

    /** @var string Key associated with destination directory */
    protected static string $destinationDirectoryKey;

    /** @var bool Is CPU affinity enabled */
    private bool $affinity;

    /** @var array|null Numbers of CPU cores to pin process to */
    private ?array $cpus;

    /** @var string Log file name to be written by plotter */
    private string $log;

    /** @var array|null Additional arguments for plotting command */
    private ?array $arguments;

    /** @var string Executable override */
    private string $executable;

    /** @var string Postfix for temporary directories e.t.c. */
    private string $postfix;

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
    public function __construct(string $postfix, string $executable, array $arguments, bool $affinity, ?array $cpus, string $log)
    {
        $this->executable = $executable;
        $this->affinity = $affinity;
        $this->cpus = $cpus;
        $this->log = $log;
        $this->arguments = $arguments;
        $this->postfix = $postfix;
    }

    /**
     * Test plotting command for proper conditions.
     *
     * @throws  PlotterException
     */
    public function test(): void
    {
        $this->makeCommand();
        $this->getLogFileName();
    }

    /**
     * Run plotting command.
     *
     * @throws  PlotterException
     * @throws  BindingResolutionException
     * @throws  SystemCommandException
     */
    public function run(): int
    {
        $command = $this->makeCommand();

        // Make taskset command if applicable
        $command = $this->systemCommands()->applyCPUAffinity(
            $command,
            $this->getAffinityEnabled(),
            $this->getAffinityCores()
        );

        // Unlink log file if it exists. It should not!
        $this->unlinkLog();

        // Run command in background and return PID
        return $this->systemCommands()->runInBackground($command, $this->getLogFileName());
    }

    /**
     * Clean up temporary directories and optionally log file.
     *
     * @throws  PlotterException
     */
    public function cleanUp(bool $saveLog = false): void
    {
        if (!$saveLog) {
            $this->unlinkLog();
        }

        foreach ($this->getTemporaryDirs() as $dir) {
            if ($dir === null || !is_dir($dir)) {
                continue;
            }

            foreach (glob($dir . DIRECTORY_SEPARATOR . "*.*") as $filename) {
                if (is_file($filename)) {
                    unlink($filename);
                }
            }

            rmdir($dir);
        }
    }

    /**
     * Cet executable.
     *
     * @return  string
     */
    protected function executable(): string
    {
        return $this->executable;
    }

    /**
     * Get list of temporary directories.
     *
     * @return  array
     *
     * @throws  PlotterException
     */
    protected function getTemporaryDirs(): array
    {
        $resolved = [];

        foreach (static::$temporaryDirectoriesKeys ?? [] as $key) {
            $resolved[] = $this->getTempDir($key, $this->postfix());
        }

        return $resolved;
    }

    /**
     * Get postfix.
     *
     * @return  string|null
     */
    protected function postfix(): ?string
    {
        return $this->postfix ?? null;
    }

    /**
     * Get name of plotting command.
     *
     * @return  string
     */
    public static function name(): string
    {
        return static::$name;
    }

    /**
     * Get arguments list applicable to this plotting command.
     *
     * @return  array
     */
    public static function getArgumentsList(): array
    {
        return static::$argumentsList ?? [];
    }

    /**
     * Get arguments defaults applicable to this plotting command.
     *
     * @return  array
     */
    public static function getArgumentsDefaults(): array
    {
        return static::$argumentsDefaults ?? [];
    }

    /**
     * Get associations of arguments with global defaults.
     *
     * @return  array
     */
    public static function getGlobalDefaultsAssociations(): array
    {
        return static::$globalDefaultsAssociations ?? [];
    }

    /**
     * Get destination directory for complete plots.
     *
     * @return  string|null
     *
     * @throws  PlotterException
     */
    public function getDestination(): ?string
    {
        return $this->getDir(static::$destinationDirectoryKey);
    }

    /**
     * Make command to run plotting.
     *
     * @throws  PlotterException
     */
    abstract protected function makeCommand(): string;

    /**
     * Get argument with checking in can be null.
     *
     * @param string $key
     * @param bool $required
     * @param array|string|null $message
     *
     * @return  mixed
     *
     * @throws  PlotterException
     *
     * @internal
     */
    protected function getArgument(string $key, bool $required = false, array|string $message = null): mixed
    {
        if ($required && !isset($this->arguments[$key])) {
            if (is_array($message)) {
                $message = sprintf(...$message);
            } else if ($message === null) {
                $message = sprintf('Argument %s does not set', $key);
            }

            throw new PlotterException('[' . static::class . '] ' . $message);
        }

        return $this->arguments[$key] ?? null;
    }

    /**
     * Get directory from argument with checking in can be null.
     *
     * @param string $key
     * @param bool $required
     * @param array|string|null $message
     *
     * @return  string
     *
     * @throws  PlotterException
     *
     * @internal
     */
    protected function getDir(string $key, bool $required = false, array|string $message = null): string
    {
        $dir = $this->getArgument($key, $required, $message);

        if (($dir !== null || $required) && !is_dir($dir)) {
            throw new PlotterException(sprintf('[%s] Directory %s does not exists.', static::class, $dir));
        }

        return $dir;
    }

    /**
     * Get directory from argument with checking in can be null.
     *
     * @param string $key
     * @param string|null $postfix
     * @param bool $create
     * @param bool $required
     * @param array|string|null $message
     *
     * @return  string
     *
     * @throws  PlotterException
     *
     * @internal
     */
    protected function getTempDir(string $key, ?string $postfix = null, bool $create = false, bool $required = false, array|string $message = null): string
    {
        $dir = $this->getArgument($key, $required, $message);

        if ($dir !== null && $postfix !== null) {
            $dir .= DIRECTORY_SEPARATOR . $postfix;
        }

        if ($create && $dir !== null && !is_dir($dir)) {
            try {
                mkdir($dir, 0777, true);

                if (!is_dir($dir)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }

                return $dir;

            } catch (Exception $e) {
                throw new PlotterException(sprintf('[%s] Can not create directory [%s]: %s', static::class, $dir, $e->getMessage()));
            }
        }

        if ($dir === null && $required) {
            throw new PlotterException(sprintf('[%s] Directory does not set for argument [%s].', static ::class, $key));
        }

        return $dir;
    }

    /**
     * Get CPU affinity enabled.
     *
     * @return  bool
     *
     * @internal
     */
    protected function getAffinityEnabled(): bool
    {
        return $this->affinity ?? false;
    }

    /**
     * Get CPU cores to pin process to.
     *
     * @return  array
     *
     * @internal
     */
    protected function getAffinityCores(): array
    {
        return $this->cpus ?? [];
    }

    /**
     * Get log file name.
     *
     * @return  string
     *
     * @throws  PlotterException
     *
     * @internal
     */
    protected function getLogFileName(): string
    {
        if (!isset($this->log)) {
            throw new PlotterException('[' . static::class . '] Log file name is empty');
        }

        $dir = pathinfo($this->log, PATHINFO_DIRNAME);

        if (!is_dir($dir)) {
            throw new PlotterException('[' . static::class . '] Log file path must be existing directory');
        }

        return $this->log;
    }

    /**
     * Unlink existing log file.
     *
     * @return  void
     *
     * @internal
     */
    protected function unlinkLog(): void
    {
        if (isset($this->log) && file_exists($this->log)) {
            unlink($this->log);
        }
    }

    /**
     * Fill options to command.
     *
     * @param string $command
     * @param array $options
     * @param string $keyValueSeparator
     * @param bool $quoteStringValue
     *
     * @return  string
     *
     * @internal
     */
    protected function fillOptions(string $command, array $options, string $keyValueSeparator = ' ', bool $quoteStringValue = false): string
    {
        foreach ($options as $key => $value) {

            if (is_bool($value)) {
                $command .= $value === true ? sprintf(' %s', $key) : null;
                continue;
            }

            $command .= $value !== null
                ? sprintf(' %s%s%s',
                    $key,
                    $keyValueSeparator,
                    $quoteStringValue && is_string($value) ? "'$value'" : (string)$value
                )
                : null;
        }

        return $command;
    }
}
