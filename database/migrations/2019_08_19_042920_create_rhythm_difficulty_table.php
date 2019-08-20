<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\RhythmDifficulty;

class CreateRhythmDifficultyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rhythm_difficulties', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('grade_id');
            $table->unsignedInteger('min_difficulty');
            $table->unsignedInteger('max_difficulty');
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades');
        });

        RhythmDifficulty::create(['grade_id' => 1, 'min_difficulty' => 50,  'max_difficulty' => 150]);
        RhythmDifficulty::create(['grade_id' => 2, 'min_difficulty' => 151, 'max_difficulty' => 250]);
        RhythmDifficulty::create(['grade_id' => 3, 'min_difficulty' => 251, 'max_difficulty' => 350]);
        RhythmDifficulty::create(['grade_id' => 4, 'min_difficulty' => 351, 'max_difficulty' => 400]);
        RhythmDifficulty::create(['grade_id' => 5, 'min_difficulty' => 401, 'max_difficulty' => 450]);
        RhythmDifficulty::create(['grade_id' => 6, 'min_difficulty' => 451, 'max_difficulty' => 500]);

        DB::statement('ALTER table `games` modify `difficulty_id` int(10) unsigned NULL;');


        Schema::table('games', function(Blueprint $table) {
            $table->unsignedInteger('rhythm_difficulty_id')->nullable();

            $table->foreign('rhythm_difficulty_id')->references('id')->on('rhythm_difficulties');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('games', function(Blueprint $table) {
            $table->dropForeign('rhythm_difficulty_id');
            $table->dropColumn('rhythm_difficulty_id');
        });

        RhythmDifficulty::truncate();

        Schema::dropIfExists('rhythm_difficulties');
    }
}
