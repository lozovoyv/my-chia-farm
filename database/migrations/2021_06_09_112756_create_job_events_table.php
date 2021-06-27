<?php
/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up(): void
    {
        Schema::create('mp_job_events', function (Blueprint $table) {
            $table->string('job_event_name');
            $table->timestamp('fire_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down(): void
    {
        Schema::dropIfExists('mp_job_events');
    }
}
