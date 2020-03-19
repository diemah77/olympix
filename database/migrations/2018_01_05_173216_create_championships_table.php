<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChampionshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('championships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('system_id');
            $table->integer('sets');
            $table->integer('registrations_count')->default(0);
            $table->integer('status')->default(1);
            $table->integer('doubles_draw_type')->default(1);
            $table->integer('active_phase')->nullable();
            $table->boolean('third_place')->default(false);
            $table->unsignedInteger('tournament_id');
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
        Schema::dropIfExists('championships');
    }
}
