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

use App\Exceptions\JobException;
use App\Exceptions\PlotterException;
use App\Exceptions\WorkerException;
use App\Http\Controllers\Controller;
use App\Traits\ReturnsJsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Job;
use App\Models\Worker;
use Illuminate\Contracts\Container\BindingResolutionException;

class WorkerController extends Controller
{
    use ReturnsJsonResponse;

    /**
     * Dismiss worker.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function dismiss(Request $request): JsonResponse
    {
        $workerId = $request->input('id');

        /** @var Worker $worker */
        $worker = Worker::query()->where('id', $workerId)->first();

        if ($worker === null) {
            return $this->error('Worker [%s] not found', $workerId);
        }

        try {
            $worker->shutdown(false, 'Dismissed by user');
        } catch (PlotterException | BindingResolutionException $e) {
            return $this->error('Error while dismissing worker [%s]: %s', $workerId, $e->getMessage());
        }

        return $this->success('Worker [%s] dismissed.', $workerId);
    }

    /**
     * Start new worker.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function start(Request $request): JsonResponse
    {
        $jobId = $request->input('job_id');
        /** @var Job $job */
        $job = Job::query()->where('id', $jobId)->first();

        if ($job === null) {
            return $this->error('Job [%s] not found', $jobId);
        }

        try {
            $job->start(1);
        } catch (JobException | BindingResolutionException | PlotterException | WorkerException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success('New worker started for %s', $job->title());
    }
}
