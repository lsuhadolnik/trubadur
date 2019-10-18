<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExerciseCachingFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rhythm_exercises', function(Blueprint $table) {
            $table->boolean('mp3_generated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rhythm_exercises', function(Blueprint $table) {
            $table->dropColumn('mp3_generated');
        });
    }
}
