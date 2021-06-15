<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_workers', function (Blueprint $table) {
            // general
            $table->id();
            $table->integer('job_id');
            $table->integer('pid')->nullable();

            // progress
            $table->integer('phase')->default(0);
            $table->integer('step')->default(0);
            $table->integer('percents')->default(0);

            // worker options
            $table->string('farmer_public_key')->nullable();
            $table->string('pool_public_key')->nullable();
            $table->smallInteger('plot_size', false, true)->default(32);
            $table->smallInteger('buckets', false, true)->default(128);
            $table->smallInteger('buffer', false, true)->default(3389);
            $table->smallInteger('threads', false, true)->default(2);
            $table->string('tmp_dir')->nullable();
            $table->string('tmp2_dir')->nullable();
            $table->string('final_dir')->nullable();
            $table->boolean('disable_bitfield')->default(false);
            $table->boolean('skip_add')->default(false);
            $table->boolean('cpu_affinity_enable')->default(false);
            $table->text('cpus')->nullable();

            $table->boolean('save_log')->default(false);

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
        Schema::dropIfExists('workers');
    }
}
