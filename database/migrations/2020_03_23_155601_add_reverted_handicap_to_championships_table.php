<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevertedHandicapToChampionshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('championships', function (Blueprint $table) {
            $table->boolean('reverted_handicap')->after('handicap')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('championships', function (Blueprint $table) {
            $table->dropColumn('reverted_handicap');
        });
    }
}
