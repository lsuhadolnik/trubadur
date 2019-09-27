<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGameColumnToBadgeUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('badge_user', function(Blueprint $table) {
            $table->unsignedInteger('game_id')->nullable();

            $table->foreign('game_id')->references('id')->on('games');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('badge_user', function(Blueprint $table) {
            $table->dropForeign('badge_user_game_id_foreign');
            $table->dropColumn('game_id');
        });
    }
}
