<?php

namespace Tests\Unit\Parser;

use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * @return void
     */
    public function test_parser()
    {
        $logSrc = require 'log.php';
        $log = null;
        $parser = new Parser('', 128);

        foreach ($logSrc as $srcLine) {
            $line = array_shift($srcLine);
            $log .= "\n$line";
            $parser->parse($log);
            $this->assertEquals($srcLine, $parser->getMetrics());
            $this->assertIsInt($parser->testPercents());
        }
    }
}
