<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('verified')->default(false);
            $table->string('email_token', 1024)->nullable();
            $table->rememberToken();
            $table->boolean('admin')->default(false);
            $table->integer('rating')->default(1000);
            $table->enum('instrument', ['clarinet', 'guitar', 'piano', 'trumpet', 'violin'])->default('piano');
            $table->integer('note_playback_delay')->default(2000);
            $table->enum('clef', ['violin', 'bass'])->default('violin');
            $table->string('avatar')->default('/images/avatars/default.svg');
            $table->integer('school_id')->unsigned();
            $table->integer('grade_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
