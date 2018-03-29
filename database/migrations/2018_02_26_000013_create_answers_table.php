<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('question_id')->unsigned();
            $table->integer('time');
            $table->integer('n_additions');
            $table->integer('n_deletions');
            $table->integer('n_playbacks');
            $table->integer('success');
            $table->timestamps();
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('game_id')->references('game_id')->on('game_user')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('game_user')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
