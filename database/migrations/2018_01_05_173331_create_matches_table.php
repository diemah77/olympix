<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('order')->nullable();
            $table->integer('status')->default(1);
            $table->integer('winner')->default(0);
            $table->unsignedInteger('set_count')->nullable();
            $table->unsignedInteger('table_id')->nullable();
            $table->unsignedInteger('tournament_id')->nullable();
            $table->unsignedInteger('championship_id')->nullable();
            $table->unsignedInteger('result_id')->nullable();
            $table->unsignedInteger('p1_id')->nullable();
            $table->unsignedInteger('p2_id')->nullable();
            $table->string('p1_type')->nullable();
            $table->string('p2_type')->nullable();
            $table->unsignedInteger('next_match_id')->nullable();
            $table->unsignedInteger('next_losers_match_id')->nullable();
            $table->unsignedInteger('matchable_id')->nullable();
            $table->string('matchable_type')->nullable();
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
        Schema::dropIfExists('matches');
    }
}
