<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRhythmExerciseBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rhythm_exercise_bars', function (Blueprint $table) {
            $table->unsignedInteger('rhythm_exercise_id');
            $table->unsignedInteger('rhythm_bar_id');
            $table->integer('seq');
            
            $table->foreign('rhythm_exercise_id')->references('id')->on('rhythm_exercises');
            $table->foreign('rhythm_bar_id')->references('id')->on('rhythm_bars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rhythm_exercise_bars');
    }
}
