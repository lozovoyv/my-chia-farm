<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Tests\Classes\Plotters;

use App\Classes\Plotters\Processor;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{

    public function testCount(): void
    {
        $test = null;
        self::assertEquals(0, Processor::count('/Bucket \d+/', $test));
        self::assertEquals(0, Processor::count('/Bucket \d+/', $test, true));

        $test = '';
        self::assertEquals(0, Processor::count('/Bucket \d+/', $test));
        self::assertEquals(0, Processor::count('/Bucket \d+/', $test, true));

        $test = <<<TEST
Starting phase 3/4: Compression from tmp files into "/temporary/temp1/plot-k32-2021-05-22-18-03-ae8a5d9725c92fef74714cedcd3798a7b88fcc7113074544f12c11d73b5f9ff7.plot.2.tmp" ... Sat May 22 22:05:05 2021
Compressing tables 1 and 2
	Bucket 0 uniform sort. Ram: 3.250GiB, u_sort min: 1.250GiB, qs min: 0.313GiB.
    Bucket 1 uniform sort. Ram: 3.250GiB, u_sort min: 1.250GiB, qs min: 0.313GiB.
    Bucket 2 uniform sort. Ram: 3.250GiB, u_sort min: 1.250GiB, qs min: 0.313GiB.
    Bucket 3 uniform sort. Ram: 3.250GiB, u_sort min: 1.250GiB, qs min: 0.313GiB.
    Bucket 4 uniform sort. Ram: 3.250GiB, u_sort min: 1.250GiB, qs min: 0.313GiB.
TEST;
        self::assertEquals(5, Processor::count('/Bucket \d+/', $test));
        self::assertEquals(5, Processor::count('/Bucket \d+/', $test, true));

        $test .= <<<TEST
    First computation pass time: 613.081 seconds. CPU (98.710%) Sat May 22 22:15:18 2021
	Bucket 0 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.329GiB.
    Bucket 1 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.300GiB.
    Bucket 2 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.287GiB.
    Bucket 3 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.306GiB.
    Bucket 4 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.317GiB.
    Bucket 5 uniform sort. Ram: 3.250GiB, u_sort min: 0.750GiB, qs min: 0.318GiB.
TEST;
        self::assertEquals(11, Processor::count('/Bucket \d+/', $test));
        self::assertEquals(6, Processor::count('/Bucket \d+/', $test, true));
    }

    public function testPart(): void
    {
        $subject = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'original.log');

        $content = Processor::part($subject, 'Starting phase 1/4', null, true);
        self::assertTrue(str_starts_with($content, 'Starting phase 1/4'));

        $content = Processor::part($subject, 'Starting phase 1/4', null, false);
        self::assertTrue(str_starts_with($content, 'Computing table 1'));

        $content = Processor::part($subject, 'Starting phase 1/4', 'Computing table 2', true, false);
        self::assertTrue(str_ends_with($content, 'F1 complete, time: 133.966 seconds. CPU (147.68%) Sat May 22 18:05:40 2021'));

        $content = Processor::part($subject, 'Starting phase 1/4', 'puting table 2', true, false);
        self::assertTrue(str_ends_with($content, 'F1 complete, time: 133.966 seconds. CPU (147.68%) Sat May 22 18:05:40 2021'));

        $content = Processor::part($subject, 'Time for phase 4', null, false, true);
        self::assertTrue(str_starts_with($content, 'Approximate working space'));
        self::assertTrue(str_ends_with(trim($content), 'to "/run/media/lv/Pocket/dest/plot-k32-2021-05-22-18-03-ae8a5d9725c92fef74714cedcd3798a7b88fcc7113074544f12c11d73b5f9ff7.plot"'));

    }
}
