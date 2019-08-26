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
            $table->timestamps();

            $table->foreign('grade_id')->references('id')->on('grades');
        });

        RhythmDifficulty::create(['id' => 10, 'grade_id' => 1]);

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
