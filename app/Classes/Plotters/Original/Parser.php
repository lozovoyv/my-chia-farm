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

use App\Classes\Plotters\BaseParser;
use App\Classes\Plotters\Processor;
use App\Exceptions\ParserException;

class Parser extends BaseParser
{
    /** @var int Number of buckets used by plotter */
    private int $buckets;

    /** @var int|null Current sub step */
    private ?int $currentSubStep;

    /** @var int|null Current bucket */
    private ?int $currentBucket;

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
        $this->buckets = $this->getArgument('-u', 128);

        if (!$this->phase5() && !$this->phase4() && !$this->phase3() && !$this->phase2() && !$this->phase1()) {
            $this->currentPhase = 0;
            $this->currentStep = 0;
            $this->currentSubStep = null;
            $this->currentBucket = null;
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
        $this->raw = Processor::part($this->log(), 'Starting phase 1/4', 'Time for phase 1 =', true, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 1;
        $this->currentStep = Processor::count('/Computing\stable\s\d/', $this->raw);
        $this->currentSubStep = null;
        $this->currentBucket = null;

        if ($this->currentStep <= 1) {
            return true;
        }

        $tmp = Processor::part($this->raw, sprintf('Computing table %s', $this->currentStep));
        $this->currentBucket = Processor::count('/Bucket\s\d+/', $tmp);

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
        $this->raw = Processor::part($this->log(), 'Starting phase 2/4', 'Time for phase 2 =', true, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 2;
        $this->currentStep = Processor::count('/Backpropagating\son\stable\s\d/', $this->raw);
        $this->currentSubStep = null;
        $this->currentBucket = null;

        if ($this->currentStep === 0) {
            return true;
        }

        $tmp = Processor::part($this->raw, sprintf('Backpropagating on table %s', 7 - ($this->currentStep - 1)));
        $this->currentSubStep = Processor::count('/scanned\stime\s=/', $tmp) + 1;

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
        $this->raw = Processor::part($this->log(), 'Starting phase 3/4', 'Time for phase 3 =', true, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 3;
        $this->currentStep = Processor::count('/Compressing\stables\s\d\sand\s\d/', $this->raw);
        $this->currentSubStep = null;
        $this->currentBucket = null;

        if ($this->currentStep === 0) {
            return true;
        }

        $tmp = Processor::part($this->raw, sprintf("Compressing tables %s and %s", $this->currentStep, $this->currentStep + 1));
        $this->currentSubStep = Processor::count('/First\scomputation\spass/', $tmp) + 1;
        if ($this->currentSubStep === 2) {
            $tmp = Processor::part($tmp, 'First computation pass');
        }
        $this->currentBucket = Processor::count('/Bucket\s\d+/', $tmp, true);

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
        $this->raw = Processor::part($this->log(), 'Starting phase 4/4', 'Time for phase 4 = ', true, true);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 4;
        $this->currentStep = Processor::count('/Writing\sC2\stable/', $this->raw) + 1;
        $this->currentSubStep = null;
        $this->currentBucket = $this->currentStep === 1 ? Processor::count('/Bucket\s\d+/', $this->raw) : null;

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
        $this->raw = Processor::part($this->log(), 'Total time = ', null, false, false);

        if ($this->raw === null) {
            return false;
        }

        $this->currentPhase = 5;
        $this->currentStep = 0;
        $this->currentSubStep = null;
        $this->currentBucket = null;

        return true;
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

        if (is_array($percents)) {
            $percents = $percents[0] + ($percents[1] - $percents[0]) / $this->buckets * ($this->currentBucket ?? 0);
        }

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
