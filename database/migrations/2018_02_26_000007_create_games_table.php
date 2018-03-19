<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('mode', ['practice', 'single', 'multi']);
            $table->enum('type', ['intervals', 'rythm']);
            $table->integer('winner_id')->unsigned()->nullable();
            $table->integer('level_id')->unsigned();
            $table->boolean('finished')->default(false);
            $table->timestamps();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
