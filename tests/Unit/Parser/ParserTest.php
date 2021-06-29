<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tests\Unit\Parser;

use Tests\Unit\Parser\Helpers\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testPlotName(): void
    {
        $parser = new Parser(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'madmax.log', []);
        self::assertEquals('plot-k32-2021-06-16-18-08-0b23fe3b8f67b75d51b2ac781d3b0c646e729d7b9d49b27100f2fe1028f39b20.plot', $parser->_getPlotFileName());

        $parser = new Parser(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'original.log', []);
        self::assertEquals('plot-k32-2021-05-22-18-03-ae8a5d9725c92fef74714cedcd3798a7b88fcc7113074544f12c11d73b5f9ff7.plot', $parser->_getPlotFileName());
    }

}
