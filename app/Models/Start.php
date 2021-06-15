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

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Start extends Model
{
    use HasFactory;

    protected $table = 'mp_starts';

    /**
     * Job related to this start.
     *
     * @return BelongsTo
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get desired number of workers to start.
     *
     * @return  int
     */
    public function numberOfWorkersToStart(): int
    {
        return $this->getAttribute('number_of_workers');
    }
}
