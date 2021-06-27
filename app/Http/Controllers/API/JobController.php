<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers\API;

use App\Exceptions\PlotterException;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Job;
use App\Models\Start;
use App\Models\Worker;
use App\Traits\ReturnsJsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class JobController extends Controller
{
    use ReturnsJsonResponse;

    /**
     * Get list of jobs with events, starts and workers.
     *
     * @return  JsonResponse
     */
    public function all(): JsonResponse
    {
        $jobs = Job::query()->with(['events', 'starts', 'workers'])->get();

        return $this->data($jobs);
    }

    /**
     * Store a newly created job.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $job = new Job;

        $job->fromRequest($request);

        $job->save();

        $this->updateJobRelations($job, $request);

        $job->load(['events', 'starts', 'workers']);

        return $this->successWithData($job, 'Job created');
    }

    /**
     * Update the specified job in storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $id = $request->input('id');

        /** @var Job $job */
        $job = Job::query()->where('id', $id)->first();

        if ($job === null) {
            return $this->error('Job [%s] not found.', $id);
        }

        $job->fromRequest($request);

        $job->save();

        $this->updateJobRelations($job, $request);

        $job->load(['events', 'starts', 'workers']);

        return $this->successWithData($job, 'Job updated');
    }

    /**
     * Remove the specified job from storage.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function remove(Request $request): JsonResponse
    {
        $id = $request->input('id');

        // shutdown all workers on job to be deleted
        $workers = Worker::query()->where('job_id', $id)->get();
        foreach ($workers as $worker) {
            /** @var Worker $worker */
            try {
                $worker->shutdown(false, 'Dismissed by user');
            } catch (PlotterException | BindingResolutionException $e) {
                return $this->error('Error while dismissing worker [%s]: %s', $worker->getAttribute('id'), $e->getMessage());
            }
        }

        Event::query()->where('job_id', $id)->delete();
        Start::query()->where('job_id', $id)->delete();
        Job::query()->where('id', $id)->delete();

        return $this->success('Job deleted');
    }

    /**
     * Update job relations to incoming relations data.
     *
     * @param Job $job
     * @param Request $request
     *
     * @return  void
     *
     * @throws  InvalidArgumentException
     *
     * @internal
     */
    protected function updateJobRelations(Job $job, Request $request): void
    {
        if (!$job->exists) {
            throw new InvalidArgumentException('Can not update relations for non-existing model');
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
        $relations = collect($existing)->keyBy('id');

        /** @var Model $class */
        $assets = $class::query()->whereIn('id', $ids)->get();

        foreach ($assets as $asset) {
            /** @var Model $asset */
            $asset->setRawAttributes($relations[$asset->getAttribute('id')]);
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
