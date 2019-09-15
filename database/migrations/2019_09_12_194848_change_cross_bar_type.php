<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

class ChangeCrossBarType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `rhythm_bars` CHANGE `cross_bar` `cross_bar` double NULL;");

        Schema::table('rhythm_features', function(Blueprint $table) {

            $table->boolean('cross_bar')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('rhythm_features', function(Blueprint $table) {

            $table->dropColumn('cross_bar');

        });
    }
}
