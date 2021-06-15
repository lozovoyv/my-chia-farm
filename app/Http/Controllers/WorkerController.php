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

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Job;
use App\Models\Worker;
use Illuminate\Contracts\Container\BindingResolutionException;

class WorkerController extends Controller
{
    /**
     * Dismiss worker.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws BindingResolutionException
     */
    public function destroy(Request $request): JsonResponse
    {
        /** @var Worker $worker */
        $worker = Worker::query()->where('id', $request->input('id'))->first();

        if ($worker === null) {
            return response()->json([], 404);
        }

        $worker->shutdown(false, 'Dismissed by user');

        return response()->json();
    }

    /**
     * Start new worker.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     *
     * @throws  BindingResolutionException
     */
    public function start(Request $request): JsonResponse
    {
        /** @var Job $job */
        $job = Job::query()->where('id', $request->input('job_id'))->first();

        if ($job === null) {
            return response()->json([], 404);
        }

        $job->start(1);

        return response()->json();
    }
}
