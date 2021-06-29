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

use App\Exceptions\ParserException;

abstract class BaseParser implements ParserInterface
{
    /** @var string Name of log file */
    private string $filename;

    /** @var array Arguments used for plotting */
    private array $arguments;

    /** @var string Log contents */
    private string $log;

    /** @var int Current phase */
    protected int $currentPhase;

    /** @var int Current step of phase */
    protected int $currentStep;

    /** @var int Current progress */
    protected int $percents;

    /** @var string|null Status text */
    protected ?string $statusText;

    /** @var bool Error indicator */
    protected bool $hasError;

    /** @var string|null Error message */
    protected ?string $errorText;

    /**
     * BaseParser constructor.
     *
     * @param string $filename
     * @param array $arguments
     */
    public function __construct(string $filename, array $arguments)
    {
        $this->filename = $filename;
        $this->arguments = $arguments;
    }

    /**
     * Get content of log file.
     *
     * @return  string
     *
     * @throws  ParserException
     */
    protected function log(): string
    {
        if (!isset($this->log)) {
            if (empty($this->filename)) {
                throw new ParserException('Log file name is empty');
            }

            if (!file_exists($this->filename)) {
                throw new ParserException('Log file does not exists');
            }

            $this->log = file_get_contents($this->filename);

            if ($this->log === false) {
                throw new ParserException('Error while reading log file');
            }
        }

        return $this->log;
    }

    /**
     * Get value of plotting argument.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return  mixed
     */
    protected function getArgument(string $key, mixed $default = null): mixed
    {
        return $this->arguments[$key] ?? $default;
    }

    /**
     * Get plot filename from log.
     *
     * @return  string|null
     *
     * @throws  ParserException
     */
    protected function getPlotFileName(): ?string
    {
        $match = null;
        $found = 1 === preg_match('/plot-k\d+-\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-[0-9a-z]+/i', $this->log(), $match);

        return $found ? $match[0] . '.plot' : null;
    }

    /**
     * Get plotting progress parsed from log.
     *
     * @return  Progress
     *
     * @throws  ParserException
     */
    public function getProgress(): Progress
    {
        $this->parse();

        return new Progress(
            $this->getCurrentPhase(),
            $this->getCurrentStep(),
            $this->getPercents(),
            $this->getStatusText(),
            $this->getPlotFileName(),
            $this->hasError(),
            $this->getErrorText()
        );
    }

    /**
     * Stub method must be overridden by real parser.
     *
     * @return  void
     */
    protected function parse(): void
    {
        $this->currentPhase = 0;
        $this->currentStep = 0;
        $this->percents = 0;
        $this->statusText = null;
        $this->hasError = 1;
        $this->errorText = 'Error in parsing functionality for this plotter';
    }

    /**
     * Get current phase. Can be overridden by real parser.
     *
     * @return  int
     */
    protected function getCurrentPhase(): int
    {
        return $this->currentPhase;
    }

    /**
     * Get current step. Can be overridden by real parser.
     *
     * @return  int
     */
    protected function getCurrentStep(): int
    {
        return $this->currentStep;
    }

    /**
     * Get current progress. Can be overridden by real parser.
     *
     * @return  int
     */
    protected function getPercents(): int
    {
        return $this->percents;
    }

    /**
     * Get current status text. Can be overridden by real parser.
     *
     * @return  string|null
     */
    protected function getStatusText(): ?string
    {
        return $this->statusText;
    }

    /**
     * Get current error state. Can be overridden by real parser.
     *
     * @return  bool
     */
    protected function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * Get current error text message. Can be overridden by real parser.
     *
     * @return  string|null
     */
    protected function getErrorText(): ?string
    {
        return $this->errorText;
    }
}
