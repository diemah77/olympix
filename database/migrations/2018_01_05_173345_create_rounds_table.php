<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('number')->nullable();
            $table->integer('order');
            $table->boolean('side')->nullable();
            $table->integer('type')->nullable();
            $table->unsignedInteger('ko_phase_id');
            $table->unsignedInteger('next_round_id')->nullable();
            $table->unsignedInteger('next_losers_round_id')->nullable();
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
        Schema::dropIfExists('rounds');
    }
}
