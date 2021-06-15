<?php

namespace Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;

class ParserPhasesTest extends TestCase
{
    /**
     * @return void
     */
    public function test_parser_phase_1()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            if ($srcLine[1] === 1) {
                $line = array_shift($srcLine);
                $log .= "\n$line";
                $this->assertEquals($srcLine, $parser->testPhase1($log));
            }
        }
    }

    /**
     * @return void
     */
    public function test_parser_phase_2()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            if ($srcLine[1] === 2) {
                $line = array_shift($srcLine);
                $log .= "\n$line";
                $this->assertEquals($srcLine, $parser->testPhase2($log));
            }
        }
    }

    /**
     * @return void
     */
    public function test_parser_phase_3()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            if ($srcLine[1] === 3) {
                $line = array_shift($srcLine);
                $log .= "\n$line";
                $this->assertEquals($srcLine, $parser->testPhase3($log));
            }
        }
    }

    /**
     * @return void
     */
    public function test_parser_phase_4()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            if ($srcLine[1] === 4) {
                $line = array_shift($srcLine);
                $log .= "\n$line";
                $this->assertEquals($srcLine, $parser->testPhase4($log));
            }
        }
    }

    /**
     * @return void
     */
    public function test_parser_phase_5()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            if ($srcLine[1] === 5) {
                $line = array_shift($srcLine);
                $log .= "\n$line";
                $this->assertEquals($srcLine, $parser->testPhase5($log));
            }
        }
    }
}
