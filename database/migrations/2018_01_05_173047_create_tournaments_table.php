<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('status')->default(1);
            $table->integer('tables_count');
            $table->integer('tables_rows')->default(1);
            $table->integer('tables_enforce_assignment')->default(1);
            $table->json('tables_transformations');
            $table->integer('championships_count')->default(0);
            $table->boolean('published')->default(0);
            $table->string('qr_code_src')->nullable();
            $table->string('hash')->nullable();
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('tournaments');
    }
}
