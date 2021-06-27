<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Events\Helpers;

use App\Models\Job;
use Illuminate\Support\Collection;

interface WorkerEventInterface
{
    /**
     * Get job assigned to event.
     *
     * @return  Job
     */
    public function job(): Job;

    /**
     * Get events associated with assigned job.
     *
     * @return  Collection|null
     */
    public function events(): ?Collection;
}
