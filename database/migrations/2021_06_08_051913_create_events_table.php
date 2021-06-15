<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_events', function (Blueprint $table) {
            $table->id();
            $table->integer('job_id');
            $table->string('name')->nullable();
            $table->boolean('on_phase')->nullable();
            $table->boolean('on_percent')->nullable();
            $table->boolean('with_delay')->nullable();
            $table->string('phase_number')->nullable();
            $table->string('phase_condition')->nullable();
            $table->integer('percent_count')->nullable();
            $table->integer('delay_seconds')->nullable();
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
        Schema::dropIfExists('mp_events');
    }
}
