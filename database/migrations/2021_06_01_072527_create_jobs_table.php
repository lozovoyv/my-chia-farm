<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->boolean('disable')->default(false);
            $table->boolean('use_global_keys')->default(true);
            $table->string('farmer_public_key')->nullable();
            $table->string('pool_public_key')->nullable();
            $table->integer('number_of_plots', false, true)->default(0);
            $table->integer('plots_done', false, true)->default(0);
            $table->boolean('use_global_plot_size')->default(true);
            $table->smallInteger('plot_size', false, true)->default(32);
            $table->boolean('use_global_buckets')->default(true);
            $table->smallInteger('buckets', false, true)->default(128);
            $table->boolean('use_global_buffer')->default(true);
            $table->smallInteger('buffer', false, true)->default(3389);
            $table->boolean('use_global_threads')->default(true);
            $table->smallInteger('threads', false, true)->default(2);
            $table->boolean('use_global_tmp_dir')->default(true);
            $table->string('tmp_dir')->nullable();
            $table->boolean('use_global_tmp2_dir')->default(true);
            $table->string('tmp2_dir')->nullable();
            $table->boolean('use_global_final_dir')->default(true);
            $table->string('final_dir')->nullable();
            $table->boolean('use_global_disable_bitfield')->default(true);
            $table->boolean('disable_bitfield')->default(false);
            $table->boolean('use_global_skip_add')->default(true);
            $table->boolean('skip_add')->default(false);
            $table->boolean('cpu_affinity_enable')->default(false);
            $table->text('cpus')->nullable();
            $table->boolean('events_disable')->default(false);
            $table->smallInteger('max_workers', false, true)->default(0);
            $table->boolean('save_worker_monitor_log')->default(false);
            $table->smallInteger('number_of_worker_logs')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
