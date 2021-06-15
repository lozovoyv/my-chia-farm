<?php
/*
 * This file is part of the MyFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Classes;

class OriginalLogParser implements LogParser
{
    protected array $structured;

    protected int $buckets;

    /** @var array|array[] Predicted percents of process progress */
    protected array $percents = [
        '0' => ['from' => 0, 'to' => 0, 'with_buckets' => false],
        '1_0' => ['from' => 0, 'to' => 0, 'with_buckets' => false],
        '1_1' => ['from' => 0, 'to' => 0.566135006151746, 'with_buckets' => false],
        '1_2' => ['from' => 0.566135006151746, 'to' => 6.40036963744953, 'with_buckets' => true], // accurate with buckets
        '1_3' => ['from' => 6.40036963744953, 'to' => 13.2133326389718, 'with_buckets' => true], // accurate with buckets
        '1_4' => ['from' => 13.2133326389718, 'to' => 21.8105508583193, 'with_buckets' => true], // accurate with buckets
        '1_5' => ['from' => 21.8105508583193, 'to' => 30.3368024555746, 'with_buckets' => true], // accurate with buckets
        '1_6' => ['from' => 30.3368024555746, 'to' => 37.7222571286106, 'with_buckets' => true], // accurate with buckets
        '1_7' => ['from' => 37.7222571286106, 'to' => 44.1236656670473, 'with_buckets' => true], // accurate with buckets
        '2_0_0' => ['from' => 44.1236656670473, 'to' => 44.1236656670473, 'with_buckets' => false],
        '2_1_1' => ['from' => 44.1236656670473, 'to' => 44.3315647217927, 'with_buckets' => false],
        '2_1_2' => ['from' => 44.3315647217927, 'to' => 44.3315647217927, 'with_buckets' => false],
        '2_2_1' => ['from' => 44.3315647217927, 'to' => 45.298931213001, 'with_buckets' => false],
        '2_2_2' => ['from' => 45.298931213001, 'to' => 48.3744082941629, 'with_buckets' => false],
        '2_3_1' => ['from' => 48.3744082941629, 'to' => 49.2696283943614, 'with_buckets' => false],
        '2_3_2' => ['from' => 49.2696283943614, 'to' => 52.1856297718736, 'with_buckets' => false],
        '2_4_1' => ['from' => 52.1856297718736, 'to' => 53.109741004185, 'with_buckets' => false],
        '2_4_2' => ['from' => 53.109741004185, 'to' => 56.0007339219177, 'with_buckets' => false],
        '2_5_1' => ['from' => 56.0007339219177, 'to' => 56.88563544621, 'with_buckets' => false],
        '2_5_2' => ['from' => 56.88563544621, 'to' => 59.7700912115448, 'with_buckets' => false],
        '2_6_1' => ['from' => 59.7700912115448, 'to' => 60.6891311983596, 'with_buckets' => false],
        '2_6_2' => ['from' => 60.6891311983596, 'to' => 63.5824044157166, 'with_buckets' => false],
        '3_0_0' => ['from' => 63.5824044157166, 'to' => 63.5824044157166, 'with_buckets' => false],
        '3_1_1' => ['from' => 63.5824044157166, 'to' => 66.284136866753, 'with_buckets' => true], // accurate with buckets
        '3_1_2' => ['from' => 66.284136866753, 'to' => 68.0687926777554, 'with_buckets' => true], // accurate with buckets
        '3_2_1' => ['from' => 68.0687926777554, 'to' => 71.7336171587353, 'with_buckets' => true], // accurate with buckets
        '3_2_2' => ['from' => 71.7336171587353, 'to' => 73.9009318985876, 'with_buckets' => true], // accurate with buckets
        '3_3_1' => ['from' => 73.9009318985876, 'to' => 77.5947707711194, 'with_buckets' => true], // accurate with buckets
        '3_3_2' => ['from' => 77.5947707711194, 'to' => 79.768107614999, 'with_buckets' => true], // accurate with buckets
        '3_4_1' => ['from' => 79.768107614999, 'to' => 83.5104138598549, 'with_buckets' => true], // accurate with buckets
        '3_4_2' => ['from' => 83.5104138598549, 'to' => 85.72055685269, 'with_buckets' => true], // accurate with buckets
        '3_5_1' => ['from' => 85.72055685269, 'to' => 89.638498994093, 'with_buckets' => true], // accurate with buckets
        '3_5_2' => ['from' => 89.638498994093, 'to' => 91.9521491009567, 'with_buckets' => true], // accurate with buckets
        '3_6_1' => ['from' => 91.9521491009567, 'to' => 94.2891128503116, 'with_buckets' => true], // accurate with buckets
        '3_6_2' => ['from' => 94.2891128503116, 'to' => 96.8886236071849, 'with_buckets' => true], // accurate with buckets
        '4_1' => ['from' => 96.8886236071849, 'to' => 98.2805962833846, 'with_buckets' => true], // accurate with buckets
        '4_2' => ['from' => 98.2805962833846, 'to' => 99.2805962833846, 'with_buckets' => false],
        '5' => ['from' => 99.2805962833846, 'to' => 100, 'with_buckets' => false],
    ];

    /** @var int Current phase */
    protected int $currentPhase;

    /** @var int|null Current step */
    protected ?int $currentStep;

    /** @var int|null Current sub step */
    protected ?int $currentSubStep;

    /** @var int|null Current bucket */
    protected ?int $currentBucket;

    /**
     * OriginalLogParser constructor.
     *
     * @param string $filename
     * @param int $buckets
     */
    public function __construct(string $filename, int $buckets = 128)
    {
        $this->buckets = $buckets;

        $this->parse(file_get_contents($filename));
    }

    /**
     * Run log file parsing.
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parse(string $rawLog): void
    {
        $p = explode('Starting phase ', $rawLog);
        if (isset($p[4])) {
            $p45 = explode('Copied final file from', $p[4]);
            unset($p[4]);
            $p = array_merge($p, $p45);
        }
        unset($p[0]);

        $phaseCount = count($p);

        $this->currentPhase = 0;
        $this->currentStep = null;
        $this->currentSubStep = null;
        $this->currentBucket = null;

        switch ($phaseCount) {
            case 1:
                $this->parsePhase1($p[1]);
                break;
            case 2:
                $this->parsePhase2($p[2]);
                break;
            case 3:
                $this->parsePhase3($p[3]);
                break;
            case 4:
                $this->parsePhase4($p[4]);
                break;
            case 5:
                $this->parsePhase5($p[5]);
                break;
            default:
        }
    }

    /**
     * Parse phase 1
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parsePhase1(string $rawLog): void
    {
        $this->currentPhase = 1;
        $this->currentStep = 0;
        $this->currentSubStep = null;
        $this->currentBucket = null;

        $tmp = explode('Computing table', $rawLog);

        unset($tmp[0]);

        $count = count($tmp);

        if ($count === 0) return;

        $matchCount = preg_match_all('/Bucket\s\d+/', $tmp[$count]);
        $this->currentStep = $count;
        $this->currentBucket = $matchCount;
    }

    /**
     * Parse phase 2
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parsePhase2(string $rawLog): void
    {
        $this->currentPhase = 2;
        $this->currentStep = 0;
        $this->currentSubStep = 0;
        $this->currentBucket = null;

        $tmp = explode('Backpropagating on table', $rawLog);
        unset($tmp[0]);

        $count = count($tmp);

        if ($count === 0) return;

        $matchCount = preg_match_all('/sorting\stable\s\d/', $tmp[$count]);
        $this->currentStep = $count;
        $this->currentSubStep = $matchCount + 1;
    }

    /**
     * Parse phase 3
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parsePhase3(string $rawLog): void
    {
        $this->currentPhase = 3;
        $this->currentStep = 0;
        $this->currentSubStep = 0;
        $this->currentBucket = null;

        $tmp = explode('Compressing tables', $rawLog);
        unset($tmp[0]);

        $count = count($tmp);

        if ($count === 0) return;

        $this->currentStep = $count;

        $v = explode('First computation pass', $tmp[$count]);

        if (isset($v[1])) {
            $this->currentSubStep = 2;
            $v = $v[1];
        } else {
            $this->currentSubStep = 1;
            $v = $v[0];
        }

        $matches = [];
        preg_match_all('/Bucket\s\d+/', $v, $matches);

        $this->currentBucket = count(array_unique($matches[0]));
    }

    /**
     * Parse phase 4
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parsePhase4(string $rawLog): void
    {
        $this->currentPhase = 4;
        $this->currentStep = 1;
        $this->currentSubStep = null;
        $this->currentBucket = null;

        $tmp = explode('Writing C2 table', $rawLog);

        if (count($tmp) === 1) {
            $this->currentBucket = preg_match_all('/Bucket\s\d+/', $tmp[0]);
        } else {
            $this->currentStep = 2;
        }
    }

    /**
     * Parse phase 5
     *
     * @param string $rawLog
     *
     * @return  void
     *
     * @internal
     */
    protected function parsePhase5(string $rawLog): void
    {
        $this->currentPhase = 5;
        $this->currentStep = null;
        $this->currentSubStep = null;
        $this->currentBucket = null;
    }

    /**
     * Get progress of plot seeding parsed from log.
     *
     * @return  int[]
     */
    public function getProgress(): array
    {
        return [
            'phase' => $this->currentPhase,
            'step' => $this->currentStep,
            '%' => $this->getPercents(),
        ];
    }

    /**
     * Calculate progress in %%.
     *
     * @return  int
     *
     * @internal
     */
    protected function getPercents(): int
    {
        $ind = "$this->currentPhase" . ($this->currentStep !== null ? "_$this->currentStep" : null) . ($this->currentSubStep !== null ? "_$this->currentSubStep" : null);

        $percents = $this->percents[$ind]['from'];

        if ($this->percents[$ind]['with_buckets']) {
            $percents += ($this->percents[$ind]['to'] - $this->percents[$ind]['from']) / $this->buckets * $this->currentBucket;
        }

        return floor($percents);
    }
}
