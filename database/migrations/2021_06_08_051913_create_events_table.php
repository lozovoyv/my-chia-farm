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

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up(): void
    {
        Schema::create('mp_events', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');

            $table->string('name')->nullable();
            $table->string('stage')->nullable();

            $table->string('p_stage')->nullable();
            $table->string('p_stage_cond')->nullable();

            $table->integer('p_progress')->nullable();

            $table->boolean('with_delay')->nullable();
            $table->integer('delay')->nullable();

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
        Schema::dropIfExists('mp_events');
    }
}
