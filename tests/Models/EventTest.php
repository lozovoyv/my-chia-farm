<?php

namespace Tests\Models;

use App\Events\WorkerStateEvent;
use App\Models\Event;
use Carbon\Carbon;
use Generator;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{

    public function testMakeEventRecord()
    {
        $event = new Event;
        $event->setAttribute('name', 'test');

        // Test no delay
        $event->setAttribute('with_delay', false);
        $event->setAttribute('delay_seconds', null);
        $rec = $event->makeEventRecord();
        $this->assertEquals('test', $rec['job_event_name']);
        $this->assertSame($rec['fire_at'], $rec['created_at']);
        $this->assertLessThan(10, Carbon::now()->unix() - $rec['fire_at']->unix());

        // Test 30 seconds delay
        $event->setAttribute('with_delay', true);
        $event->setAttribute('delay_seconds', 30);
        $rec = $event->makeEventRecord();
        $this->assertEquals('test', $rec['job_event_name']);
        $this->assertSame($rec['fire_at']->unix(), $rec['created_at']->unix() + 30);
        $diff = $rec['fire_at']->unix() - Carbon::now()->unix();
        $this->assertLessThan(35, $diff);
        $this->assertGreaterThan(25, $diff);

    }

    /**
     * @dataProvider changes
     */
    public function testIsTimeToFireOnPhaseChange($changes)
    {
        $changes = new WorkerStateEvent(...$changes);

        $event = new Event;

        $event->setAttribute('on_phase', true);
        $event->setAttribute('phase_condition', 'start');
        $event->setAttribute('on_percent', null);
        $event->setAttribute('percent_count', null);

        // phase 1 start
        $event->setAttribute('phase_number', '1_0');
        $condition = $changes->oldPhase === 0 && $changes->newPhase >= 1;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 1 start matching');

        // phase 3 start
        $event->setAttribute('phase_number', '3_0');
        $condition = $changes->oldPhase < 3 && $changes->newPhase >= 3;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 3 start matching');

        // phase 5 start
        $event->setAttribute('phase_number', '5_0');
        $condition = $changes->oldPhase < 5 && $changes->newPhase >= 5;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 5 start matching');

        $event->setAttribute('phase_condition', 'end');

        // phase 1 end
        $event->setAttribute('phase_number', '1_0');
        $condition = $changes->oldPhase <= 1 && $changes->newPhase > 1;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 1 end matching');

        // phase 3 end
        $event->setAttribute('phase_number', '3_0');
        $condition = $changes->oldPhase <= 3 && $changes->newPhase > 3;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 3 end matching');

        // phase 5 end
        $event->setAttribute('phase_number', '5_0');
        $condition = $changes->oldPhase <= 5 && $changes->newPhase > 5;
        $this->assertEquals($condition, $event->isTimeToFire($changes), 'Wrong phase 5 end matching');
    }

    /**
     * @dataProvider changes
     */
    public function testIsTimeToFireOnPhaseAndStepChange($changes, $id)
    {
        $c = new WorkerStateEvent(...$changes);

        $event = new Event;

        $event->setAttribute('on_phase', true);
        $event->setAttribute('on_percent', null);
        $event->setAttribute('percent_count', null);

        $event->setAttribute('phase_condition', 'start');

        $event->setAttribute('phase_number', "1_1");
        $condition = in_array($id, [1, 109, 203], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step start matching for asset [$id]");

        $event->setAttribute('phase_number', "1_7");
        $condition = in_array($id, [44, 152, 246], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step start matching for asset [$id]");

        $event->setAttribute('phase_number', "2_6");
        $condition = in_array($id, [61, 160, 263], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step start matching for asset [$id]");

        $event->setAttribute('phase_number', "3_6");
        $condition = in_array($id, [96, 194, 265], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step start matching for asset [$id]");

        $event->setAttribute('phase_number', "4_1");
        $condition = in_array($id, [102, 200, 265], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step start matching for asset [$id]");

        $event->setAttribute('phase_condition', 'end');

        $event->setAttribute('phase_number', "1_1");
        $condition = in_array($id, [2, 110, 204], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step end matching for asset [$id]");

        $event->setAttribute('phase_number', "1_7");
        $condition = in_array($id, [44, 160, 254], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step end matching for asset [$id]");

        $event->setAttribute('phase_number', "2_6");
        $condition = in_array($id, [63, 160, 265], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step end matching for asset [$id]");

        $event->setAttribute('phase_number', "2_1");
        $condition = in_array($id, [44, 160, 256], true);
        $this->assertEquals($condition, $event->isTimeToFire($c), "Wrong phase and step end matching for asset [$id]");
    }

    /**
     * @dataProvider changes
     */
    public function testIsTimeToFireOnPercents($changes)
    {
        $changes = new WorkerStateEvent(...$changes);

        $event = new Event;

        $event->setAttribute('on_phase', null);
        $event->setAttribute('on_percent', true);

        // phase 1 start
        foreach ([1, 2, 20, 50, 80, 99, 100] as $percent) {
            $event->setAttribute('percent_count', $percent);
            $condition = $changes->oldProgress < $percent && $changes->newProgress >= $percent;
            $this->assertEquals($condition, $event->isTimeToFire($changes), "Wrong matching for $percent%");
        }

    }

    public function changes(): Generator
    {
        $events = require 'assets/changes.php';

        foreach ($events as $id => $event) {

            yield [$event, $id];
        }
    }
}
