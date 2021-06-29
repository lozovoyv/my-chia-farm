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

use App\Classes\Plotters\BaseParser;
use App\Classes\Plotters\Processor;
use App\Exceptions\ParserException;

class Parser extends BaseParser
{
    /** @var int|null Current sub step */
    private ?int $currentSubStep;

    /** @var string|null Cache for current log part */
    private ?string $raw;

    /**
     * Parse log file.
     *
     * @return  void
     * @throws  ParserException
     *
     * @internal
     */
    protected function parse(): void
    {
        if (!$this->phase5() && !$this->phase4() && !$this->phase3() && !$this->phase2() && !$this->phase1()) {
            $this->currentPhase = 0;
            $this->currentStep = 0;
            $this->currentSubStep = null;
            $this->percents = 0;
            $this->statusText = 'Waiting for plotting started';
            $this->hasError = false;
            $this->errorText = null;

            return;
        }

        $this->calcPercents();
        $this->makeStatusText();
        $this->checkForErrors();
    }

    /**
     * Parse phase 1
     *
     * @return  bool
     *
     * @throws  ParserException
     *
     * @internal
     */
    private function phase1(): bool
    {
        $this->raw = Processor::part($this->log(), 'Plot Name: ', 'Phase 1 took ', false, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 1;
        $this->currentStep = max(7, Processor::count('/Table\s\d/', $this->raw) + 1);
        $this->currentSubStep = null;

        return true;
    }

    /**
     * Parse phase 2
     *
     * @return  bool
     *
     * @throws  ParserException
     *
     * @internal
     */
    private function phase2(): bool
    {
        $this->raw = Processor::part($this->log(), 'Phase 1 took ', 'Phase 2 took ', false, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 2;
        $this->calc23Step();

        return true;
    }

    /**
     * Parse phase 3
     *
     * @return  bool
     *
     * @throws  ParserException
     *
     * @internal
     */
    private function phase3(): bool
    {
        $this->raw = Processor::part($this->log(), 'Phase 2 took ', 'Phase 3 took ', false, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 3;
        $this->calc23Step();

        return true;
    }

    /**
     * Parse phase 4
     *
     * @return  bool
     *
     * @throws  ParserException
     *
     * @internal
     */
    private function phase4(): bool
    {
        $this->raw = Processor::part($this->log(), 'Phase 3 took ', 'Phase 4 took ', false, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 4;
        $this->currentStep = Processor::count('/Finished\swriting\sC1\sand\sC3\stables/', $this->raw) + 1;
        $this->currentSubStep = null;

        return true;
    }

    /**
     * Parse phase 5
     *
     * @return  bool
     *
     * @throws  ParserException
     *
     * @internal
     */
    private function phase5(): bool
    {
        $this->raw = Processor::part($this->log(), 'Total plot creation time was', null, false, false);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 5;
        $this->currentStep = 0;
        $this->currentSubStep = null;

        return true;
    }

    /**
     * Calc step and sub-step for phases 2 and 3.
     *
     * @return  void
     *
     * @internal
     */
    private function calc23Step(): void
    {
        $count = Processor::count('/Table\s\d/', $this->raw);
        $step = intdiv($count + 2, 2);
        $sub = ($count) % 2 + 1;

        $this->currentStep = max($step, 6);
        $this->currentSubStep = $step === 7 ? 2 : $sub;
    }

    /**
     * Calculate progress in %%.
     *
     * @return  void
     *
     * @internal
     */
    private function calcPercents(): void
    {
        $index = $this->currentPhase . ($this->currentStep !== null ? "_$this->currentStep" : null) . ($this->currentSubStep !== null ? "_$this->currentSubStep" : null);

        $data = require 'progress.php';

        $percents = $data[$index];

        $this->percents = floor($percents);
    }

    /**
     * Make string representation of status.
     *
     * @return  void
     */
    private function makeStatusText(): void
    {
        $index = $this->currentPhase . '_' . $this->currentStep;

        $data = require 'events.php';

        $this->statusText = $data[$index] ?? null;
    }

    /**
     * Check log for tailing errors.
     *
     * @return  void
     */
    private function checkForErrors(): void
    {
        // TODO test for errors
        $this->hasError = false;
        $this->errorText = null;
    }
}
