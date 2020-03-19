<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('draw_type')->nullable();
            $table->integer('status')->nullable();
            $table->integer('order')->nullable();
            $table->unsignedInteger('championship_id');
            $table->unsignedInteger('next_phase_id')->nullable();
            $table->integer('phaseable_id')->nullable();
            $table->string('phaseable_type')->nullable();
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
        Schema::dropIfExists('phases');
    }
}
