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

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up(): void
    {
        Schema::create('mp_workers', function (Blueprint $table) {
            // general
            $table->id();
            $table->integer('job_id');
            $table->integer('pid')->nullable();
            // worker options
            $table->string('plotter_alias')->nullable();
            $table->string('executable')->nullable();
            $table->text('attributes')->nullable();
            $table->boolean('cpu_affinity_enable')->default(false)->nullable();
            $table->text('cpus')->nullable();
            $table->boolean('save_log')->default(false)->nullable();

            // progress
            $table->integer('phase')->default(0);
            $table->integer('step')->default(0);
            $table->integer('percents')->default(0);
            $table->string('plot_file_name')->nullable();
            $table->boolean('running_pre_command')->nullable();
            $table->boolean('running_post_command')->nullable();
            $table->boolean('has_error')->nullable();
            $table->string('error')->nullable();
            $table->string('status')->nullable();


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
        Schema::dropIfExists('workers');
    }
}
