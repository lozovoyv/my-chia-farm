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

class Progress
{
    /** @var int Phase */
    protected int $phase;

    /** @var int Step */
    protected int $step;

    /** @var int Progress in %% */
    protected int $progress;

    /** @var string|null Error message */
    protected ?string $plotFileName;

    /** @var bool Is error */
    protected bool $hasError;

    /** @var string|null Error message */
    protected ?string $errorMessage;

    /** @var string|null Text state message */
    protected ?string $state;

    /**
     * Progress constructor.
     *
     * @param int $phase
     * @param int $step
     * @param int $progress
     * @param string|null $state
     * @param string|null $plotFileName
     * @param bool $hasError
     * @param string|null $errorMessage
     */
    public function __construct(int $phase, int $step, int $progress, ?string $state, ?string $plotFileName = null, bool $hasError = false, ?string $errorMessage = null)
    {
        $this->phase = $phase;
        $this->step = $step;
        $this->progress = $progress;
        $this->state = $state;
        $this->hasError = $hasError;
        $this->errorMessage = $errorMessage;
        $this->plotFileName = $plotFileName;
    }

    /**
     * Get phase number.
     *
     * @return  int
     */
    public function phase(): int
    {
        return $this->phase;
    }

    /**
     * Get step number.
     *
     * @return  int
     */
    public function step(): int
    {
        return $this->step;
    }

    /**
     * Get progress in percents.
     *
     * @return  int
     */
    public function progress(): int
    {
        return $this->progress;
    }

    /**
     * Get state text.
     *
     * @return  string|null
     */
    public function state(): ?string
    {
        return $this->state;
    }

    /**
     * Get plot file name.
     *
     * @return  string|null
     */
    public function plotFileName(): ?string
    {
        return $this->plotFileName;
    }

    /**
     * Whether thr process has error now.
     *
     * @return  bool
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * Get error message.
     *
     * @return  string|null
     */
    public function errorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
