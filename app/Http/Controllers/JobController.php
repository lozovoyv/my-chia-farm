<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Job;
use App\Models\Start;
use App\Models\Worker;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JobController extends Controller
{
    /** @var array|string[] Filter for incoming job data */
    protected static array $fieldFilter = [
        'title',
        'disable',
        'use_global_keys',
        'farmer_public_key',
        'pool_public_key',
        'number_of_plots',
        'plots_done',
        'plot_size',
        'use_global_plot_size',
        'buckets',
        'use_global_buckets',
        'buffer',
        'use_global_buffer',
        'threads',
        'use_global_threads',
        'tmp_dir',
        'use_global_tmp_dir',
        'tmp2_dir',
        'use_global_tmp2_dir',
        'final_dir',
        'use_global_final_dir',
        'skip_add',
        'use_global_disable_bitfield',
        'disable_bitfield',
        'use_global_skip_add',
        'cpu_affinity_enable',
        'cpus',
        'events_disable',
        'max_workers',
        'save_worker_monitor_log',
        'number_of_worker_logs',
    ];

    /**
     * Get list of jobs with events, starts and workers.
     *
     * @return  JsonResponse
     */
    public function get(): JsonResponse
    {
        $jobs = Job::query()->with(['events', 'starts', 'workers'])->get();

        return response()->json($jobs);
    }

    /**
     * Store a newly created job in storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  Exception
     */
    public function store(Request $request): JsonResponse
    {
        $job = new Job;

        $job = $this->fillJobFromRequest($job, $request);

        $job->save();

        $this->updateJobRelations($job, $request);

        $job->load(['events', 'starts']);

        return response()->json($job);
    }

    /**
     * Update the specified job in storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  Exception
     */
    public function update(Request $request): JsonResponse
    {
        /** @var Job $job */
        $job = Job::query()->where('id', $request->input('id'))->firstOrFail();

        $job = $this->fillJobFromRequest($job, $request);

        $job->save();

        $this->updateJobRelations($job, $request);

        $job->load(['events', 'starts']);

        return response()->json($job);
    }

    /**
     * Remove the specified job from storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  BindingResolutionException
     */
    public function remove(Request $request): JsonResponse
    {
        $id = $request->input('id');

        // shutdown all workers on job to be deleted
        $workers = Worker::query()->where('job_id', $id)->get();
        foreach ($workers as $worker) {
            /** @var Worker $worker */
            $worker->shutdown(false, 'Shutting down worker on job deletion');
        }

        Job::query()->where('id', $id)->delete();
        Event::query()->where('job_id', $id)->delete();
        Start::query()->where('job_id', $id)->delete();

        return response()->json();
    }

    /**
     * Fill job data from request.
     *
     * @param Job $job
     * @param Request $request
     *
     * @return  Job
     *
     * @internal
     */
    protected function fillJobFromRequest(Job $job, Request $request): Job
    {
        $newAttributes = $request->only(self::$fieldFilter);

        $attributes = $job->getAttributes();

        // this fix is needed to cast cpus from json to array
        $pinnedCPUs = $newAttributes['cpus'] ?? [];
        unset($newAttributes['cpus']);

        $job->setRawAttributes(array_merge($attributes, $newAttributes));
        $job->setAttribute('cpus', $pinnedCPUs);

        return $job;
    }

    /**
     * Update job relations to incoming relations data.
     *
     * @param Job $job
     * @param Request $request
     *
     * @return  void
     *
     * @throws  Exception
     *
     * @internal
     */
    protected function updateJobRelations(Job $job, Request $request): void
    {
        if (!$job->exists) {
            throw new Exception('Can not update relations for non-existing model');
        }

        // Make separation of incoming events to newly created and existing
        $incomingEvents = $this->sortIncomingRelation($request->input('events', []));

        // Delete removed events
        $this->deleteNonExistingRelations(Event::class, $incomingEvents['existing_ids'], $job->getAttribute('id'));
        // Update existing events
        $this->updateExistingRelations(Event::class, $incomingEvents['existing'], $incomingEvents['existing_ids']);
        // Add new events
        $this->addNewRelations(Event::class, $incomingEvents['new'], $job->getAttribute('id'));

        // Make separation of incoming starts to newly created and existing
        $incomingStarts = $this->sortIncomingRelation($request->input('starts', []));

        // Delete removed starts
        $this->deleteNonExistingRelations(Start::class, $incomingStarts['existing_ids'], $job->getAttribute('id'));
        // Update existing starts
        $this->updateExistingRelations(Start::class, $incomingStarts['existing'], $incomingStarts['existing_ids']);
        // Add new starts
        $this->addNewRelations(Start::class, $incomingStarts['new'], $job->getAttribute('id'));
    }

    /**
     * Sort incoming relation items to new and existing.
     *
     * @param array $relations
     *
     * @return  array
     *
     * @internal
     */
    protected function sortIncomingRelation(array $relations): array
    {
        $new = [];
        $existing = [];
        $ids = [];

        foreach ($relations as $relation) {
            if (isset($relation['id'])) {
                $existing[$relation['id']] = $relation;
                $ids[] = $relation['id'];
            } else {
                $new[] = $relation;
            }
        }

        return [
            'new' => $new,
            'existing' => $existing,
            'existing_ids' => $ids,
        ];
    }

    /**
     * Remove non-existing (removed) assets from database.
     *
     * @param string $class
     * @param array $ids
     * @param int $jobId
     *
     * @return  void
     *
     * @internal
     */
    protected function deleteNonExistingRelations(string $class, array $ids, int $jobId): void
    {
        /** @var Model $class */
        $class::query()->where('job_id', $jobId)->whereNotIn('id', $ids)->delete();
    }

    /**
     * Update existing assets to database.
     *
     * @param string $class
     * @param array $existing
     * @param array $ids
     *
     * @return  void
     *
     * @internal
     */
    protected function updateExistingRelations(string $class, array $existing, array $ids): void
    {
        $existing = collect($existing)->keyBy('id');

        /** @var Model $class */
        $assets = $class::query()->whereIn('id', $ids)->get();

        foreach ($assets as $asset) {
            /** @var Model $asset */
            $asset->setRawAttributes($existing[$asset->getAttribute('id')]);
            $asset->save();
        }
    }

    /**
     * Add new assets to database.
     *
     * @param string $class
     * @param array $new
     * @param int $jobId
     *
     * @return  void
     *
     * @internal
     */
    protected function addNewRelations(string $class, array $new, int $jobId): void
    {
        foreach ($new as $data) {
            /** @var Model $asset */
            $asset = new $class;
            $asset->setRawAttributes($data);
            $asset->setAttribute('job_id', $jobId);
            $asset->save();
        }
    }
}
