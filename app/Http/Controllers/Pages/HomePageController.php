<?php
/*
 * This file is part of the MyChiaFarm project.
 *
 *   (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Inertia\Inertia;
use Inertia\Response;

class HomePageController extends Controller
{
    /**
     * Get dashboard page.
     *
     * @return  Response
     */
    public function page(): Response
    {
        $jobs = Job::query()->with(['events', 'starts', 'workers'])->get();

        return Inertia::render('Dashboard', [
            'jobs_original' => $jobs,
        ]);
    }
}
