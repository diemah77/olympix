<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seed')->default(0);
            $table->integer('defeats')->default(0);
            $table->boolean('bye')->default(0);
            $table->boolean('available')->default(0);
            $table->unsignedInteger('participantable_id')->nullable();
            $table->string('participantable_type')->nullable();
            $table->unsignedInteger('championship_id');
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
        Schema::dropIfExists('participants');
    }
}
