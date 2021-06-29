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
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $table = 'mp_events';

    protected $casts = [
        'name' => 'string',
        'stage' => 'string',
        'p_stage' => 'string',
        'p_stage_cond' => 'string',
        'p_progress' => 'int',
        'with_delay' => 'bool',
        'delay' => 'int',
    ];

    /**
     * Get event name.
     *
     * @return  string
     */
    public function name(): string
    {
        return $this->getAttribute('name');
    }


    /**
     * Write record for job event needs to be fired with proper fire time.
     *
     * @return  void
     */
    public function writeEventRecord(): void
    {
        $now = Carbon::now();

        DB::table('mp_job_events')->insert([
            'job_event_name' => $this->getAttribute('name'),
            'fire_at' => $this->fireTime($now),
            'created_at' => $now,
        ]);
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
            ? $now->clone()->addSeconds($this->getAttribute('delay'))
            : $now;
    }

    /**
     * Check if stage matches.
     *
     * PST: 'plotting stage',
     * PPR: 'plotting progress',
     * WBS: 'pre-process command started',
     * WBE: 'pre-process command finished',
     * WPS: 'plotter process started',
     * WPE: 'plotter process finished',
     * WAS: 'post-process command started',
     * WAE: 'post-process command finished',
     * WE: 'worker end',
     * JE: 'job end',
     *
     * @param string $stage
     *
     * @return  bool
     */
    public function isStage(string $stage): bool
    {
        return $this->getAttribute('stage') === $stage;
    }

    /**
     * Check if this event should be fired on current state changes.
     *
     * @param WorkerStateEvent $changed
     *
     * @return  bool
     */
    public function isTimeToFire(WorkerStateEvent $changed): bool
    {
        if ($this->isStage('PST')) { // on phase condition

            $number = explode('_', $this->getAttribute('p_stage'));
            $onPhase = (int)$number[0];
            $onStep = isset($number[1]) ? (int)$number[1] : 0;
            $condition = $this->getAttribute('p_stage_cond');

            if ($onStep === 0) { // only on phase change
                if ($this->isPhaseChanged($changed) && (
                        ($condition === 'start' && $this->isPhaseStarted($changed, $onPhase))
                        || ($condition === 'finish' && $this->isPhaseEnded($changed, $onPhase))
                    )
                ) {
                    return true;
                }
            } else { // on step change
                if ($this->isPhaseOrStepChanged($changed) && (
                        ($condition === 'start' && $this->isPhaseAndStepStarted($changed, $onPhase, $onStep))
                        || ($condition === 'finish' && $this->isPhaseAndStepEnded($changed, $onPhase, $onStep))
                    )
                ) {
                    return true;
                }
            }

        } else if ($this->isStage('PPR')) { // on progress condition

            $onPercents = $this->getAttribute('p_progress');

            if ($changed->oldProgress < $onPercents && $onPercents <= $changed->newProgress) {
                return true;
            }

        } // else wrong case

        return false;
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
