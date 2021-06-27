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

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function  up(): void
    {
        Schema::create('mp_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();

            // General
            $table->integer('plots_to_do', false, true)->nullable();
            $table->integer('plots_done', false, true)->nullable();
            $table->string('plotter_alias')->nullable();

            $table->boolean('disable_workers_start')->nullable();
            $table->smallInteger('max_workers', false, true)->nullable();
            $table->boolean('disable_events_emitting')->nullable();

            // Replotting
            $table->boolean('remove_oldest')->nullable();
            $table->timestamp('removing_stop_ts')->nullable();

            // Commands
            $table->boolean('pre_command_enabled')->nullable();
            $table->string('pre_command')->nullable();
            $table->boolean('post_command_enabled')->nullable();
            $table->string('post_command')->nullable();

            // Attributes
            $table->text('arguments')->nullable();
            $table->text('use_globals_for')->nullable();

            // CPU affinity
            $table->boolean('cpu_affinity_enable')->nullable();
            $table->text('cpus')->nullable();

            // Logging
            $table->boolean('save_worker_log')->nullable();
            $table->smallInteger('number_of_worker_logs')->nullable();

            // Advanced
            $table->boolean('use_default_executable')->nullable();
            $table->string('executable')->nullable();

            // System
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
}
