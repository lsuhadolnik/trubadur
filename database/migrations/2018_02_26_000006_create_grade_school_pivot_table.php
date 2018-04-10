<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeSchoolPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grade_school', function (Blueprint $table) {
            $table->integer('grade_id')->unsigned()->index();
            $table->integer('school_id')->unsigned()->index();
            $table->primary(['grade_id', 'school_id']);
            $table->integer('difficulty_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('grade_school', function (Blueprint $table) {
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
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
        Schema::dropIfExists('grade_school');
    }
}
