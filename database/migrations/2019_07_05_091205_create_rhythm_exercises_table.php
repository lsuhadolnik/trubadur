<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRhythmExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('bar_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->json('bar_info');
            $table->unsignedInteger('min_rhythm_level');
        });

        Schema::create('rhythm_bars', function (Blueprint $table) {
            $table->increments('id');
            $table->json('content');
            $table->double('length');
            $table->boolean('cross_bar')->default(false);

            $table->softDeletes();
        });


        Schema::create('rhythm_features', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('min_occurrences')->nullable();
            $table->unsignedInteger('max_occurrences')->nullable();
        });

        Schema::create('rhythm_bar_occurrences', function (Blueprint $table) {
            
            $table->unsignedInteger('rhythm_bar_id');
            $table->unsignedInteger('rhythm_feature_id');
            $table->double('bar_probability')->default(0.5);

            $table->foreign('rhythm_bar_id')->references('id')->on('rhythm_bars');
            $table->foreign('rhythm_feature_id')->references('id')->on('rhythm_features');

            $table->unique(['rhythm_bar_id', 'rhythm_feature_id'], 'rhythm_bar_feature_unique');
        });

        Schema::create('rhythm_feature_occurrences', function (Blueprint $table) {
            
            $table->unsignedInteger('rhythm_level');
            $table->unsignedInteger('rhythm_feature_id');
            $table->unsignedInteger('bar_info_id');
            $table->double('category_probability')->default(0.5);

            $table->foreign('bar_info_id')->references('id')->on('bar_infos');
            $table->foreign('rhythm_feature_id')->references('id')->on('rhythm_features');

            $table->unique(['rhythm_level', 'rhythm_feature_id', 'bar_info_id'], 'rhythm_level_feature_barInfo_unique');
        });


        Schema::create('rhythm_exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bar_info_id');
            $table->integer('BPM');
            $table->unsignedInteger('rhythm_level');
            $table->timestamps();

            $table->foreign('bar_info_id')->references('id')->on('bar_infos');
        });

        Schema::create('rhythm_exercise_bars', function (Blueprint $table) {
            $table->unsignedInteger('rhythm_exercise_id');
            $table->unsignedInteger('rhythm_bar_id');
            $table->integer('seq');
            
            $table->foreign('rhythm_exercise_id')->references('id')->on('rhythm_exercises');
            $table->foreign('rhythm_bar_id')->references('id')->on('rhythm_bars');
        });
        


        DB::statement('ALTER table `games` modify `difficulty_id` int(10) unsigned NULL;');

        Schema::table('games', function(Blueprint $table) {
            $table->unsignedInteger('rhythm_level')->nullable();
        });

        Schema::table('users', function(Blueprint $table) {
            $table->unsignedInteger("rhythm_level")->default(11);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('rhythm_level');
        });

        Schema::table('games', function(Blueprint $table) {
            $table->dropColumn('rhythm_level');
        });


        Schema::dropIfExists('rhythm_exercise_bars');
        Schema::dropIfExists('rhythm_exercises');
        Schema::dropIfExists('rhythm_feature_occurrences');
        Schema::dropIfExists('rhythm_bar_occurrences');
        Schema::dropIfExists('rhythm_features');
        Schema::dropIfExists('rhythm_bars');
        Schema::dropIfExists('bar_infos');
    }
}
