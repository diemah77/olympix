<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupPhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_phases', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_size')->nullable();
            $table->integer('groups_count')->default(0);
            $table->integer('qualified_id')->nullable();
            $table->integer('extra_qualified_id')->nullable();
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
        Schema::dropIfExists('group_phases');
    }
}
