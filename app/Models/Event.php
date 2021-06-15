<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Models;

use App\Events\WorkerStateEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'mp_events';

    protected $casts = [
        'name' => 'string',
        'on_phase' => 'bool',
        'on_percent' => 'bool',
        'with_delay' => 'bool',
        'phase_number' => 'string',
        'phase_condition' => 'string',
        'percent_count' => 'int',
        'delay_seconds' => 'int',
    ];

    /**
     * Check if this event should be fired on current state changes.
     *
     * @param WorkerStateEvent $changed
     *
     * @return  bool
     */
    public function isTimeToFire(WorkerStateEvent $changed): bool
    {
        if ($this->getAttribute('on_phase')) { // on phase condition

            $number = explode('_', $this->getAttribute('phase_number'));
            $onPhase = (int)$number[0];
            $onStep = isset($number[1]) ? (int)$number[1] : 0;
            $condition = $this->getAttribute('phase_condition');

            if ($onStep === 0) { // only on phase change
                if ($this->isPhaseChanged($changed) && (
                        $condition === 'starts' && $this->isPhaseStarted($changed, $onPhase)
                        || $condition === 'ends' && $this->isPhaseEnded($changed, $onPhase)
                    )
                ) {
                    return true;
                }
            } else { // on step change
                if ($this->isPhaseOrStepChanged($changed) && (
                        $condition === 'starts' && $this->isPhaseAndStepStarted($changed, $onPhase, $onStep)
                        || $condition === 'ends' && $this->isPhaseAndStepEnded($changed, $onPhase, $onStep)
                    )
                ) {
                    return true;
                }
            }

        } else if ($this->getAttribute('on_percent')) { // on progress condition

            $onPercents = $this->getAttribute('percent_count');

            if ($changed->oldProgress < $onPercents && $onPercents <= $changed->newProgress) {
                return true;
            }

        } // else wrong case

        return false;
    }

    /**
     * Make record for job event needs to be fired with proper fire time.
     *
     * @return  array
     */
    public function makeEventRecord(): array
    {
        $now = Carbon::now();

        return [
            'job_event_name' => $this->getAttribute('name'),
            'fire_at' => $this->fireTime($now),
            'created_at' => $now,
        ];
    }

    /**
     * Make fire time.
     *
     * @param Carbon $now
     *
     * @return  Carbon
     *
     * @internal
     */
    protected function fireTime(Carbon $now): Carbon
    {
        return $this->getAttribute('with_delay')
            ? $now->clone()->addSeconds($this->getAttribute('delay_seconds'))
            : $now;
    }

    /**
     * Check is phase changed.
     *
     * @param WorkerStateEvent $changed
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseChanged(WorkerStateEvent $changed): bool
    {
        return $changed->oldPhase !== $changed->newPhase;
    }

    /**
     * Check is phase started.
     *
     * @param WorkerStateEvent $changed
     * @param int $phase
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseStarted(WorkerStateEvent $changed, int $phase): bool
    {
        return $changed->oldPhase < $phase
            && $phase <= $changed->newPhase;
    }

    /**
     * Check is phase ended
     *
     * @param WorkerStateEvent $changed
     * @param int $phase
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseEnded(WorkerStateEvent $changed, int $phase): bool
    {
        return $changed->oldPhase <= $phase
            && $phase < $changed->newPhase;
    }

    /**
     * Check step or phase changed
     *
     * @param WorkerStateEvent $changed
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseOrStepChanged(WorkerStateEvent $changed): bool
    {
        return $this->isPhaseChanged($changed) || $changed->oldStep !== $changed->newStep;
    }

    /**
     * Check phase and step started.
     *
     * @param WorkerStateEvent $changed
     * @param int $phase
     * @param int $step
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseAndStepStarted(WorkerStateEvent $changed, int $phase, int $step): bool
    {
        // phase not changed, step changed
        if ($changed->oldPhase === $phase && $changed->oldStep < $step && $changed->newPhase === $phase && $changed->newStep >= $step) {
            return true;
        }
        // or phase changed to target and step matches
        if ($changed->oldPhase < $phase && $changed->newPhase === $phase && $changed->newStep >= $step) {
            return true;
        }
        // or phase skipped from earlier phase
        if ($changed->oldPhase < $phase && $changed->newPhase > $phase) {
            return true;
        }
        // or phase skipped from target with earlier step
        if ($changed->oldPhase === $phase && $changed->oldStep < $step && $changed->newPhase > $phase) {
            return true;
        }

        return false;
    }

    /**
     * Check phase and step ended.
     *
     * @param WorkerStateEvent $changed
     * @param int $phase
     * @param int $step
     *
     * @return  bool
     *
     * @internal
     */
    protected function isPhaseAndStepEnded(WorkerStateEvent $changed, int $phase, int $step): bool
    {
        // phase not changed, step changed
        if ($changed->oldPhase === $phase && $changed->oldStep <= $step && $changed->newPhase === $phase && $changed->newStep > $step) {
            return true;
        }
        // or phase changed to new earlier step matches
        if ($changed->oldPhase === $phase && $changed->oldStep <= $step && $changed->newPhase > $phase) {
            return true;
        }
        // or phase skipped from earlier phase
        if ($changed->oldPhase < $phase && $changed->newPhase > $phase) {
            return true;
        }
        // or phase changed to target with step skipping
        if ($changed->oldPhase < $phase && $changed->newPhase === $phase && $changed->newStep > $step) {
            return true;
        }

        return false;
    }
}
