<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_user', function (Blueprint $table) {
            $table->integer('game_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->primary(['game_id', 'user_id']);
            $table->enum('instrument', ['clarinet', 'guitar', 'piano', 'trumpet', 'violin']);
            $table->integer('points')->default(0);
            $table->boolean('finished')->default(false);
            $table->timestamps();
        });

        Schema::table('game_user', function (Blueprint $table) {
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_user');
    }
}
