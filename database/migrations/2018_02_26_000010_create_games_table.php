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
            $table->integer('difficulty_id')->unsigned();
            $table->enum('mode', ['practice', 'single', 'multi']);
            $table->enum('type', ['intervals', 'rythm']);
            $table->timestamps();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreign('difficulty_id')->references('id')->on('difficulties')->onDelete('cascade')->onUpdate('cascade');
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
