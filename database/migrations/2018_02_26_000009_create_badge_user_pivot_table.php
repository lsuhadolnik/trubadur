<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBadgeUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('badge_user', function (Blueprint $table) {
            $table->integer('badge_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->primary(['badge_id', 'user_id']);
            $table->timestamps();
        });

        Schema::table('badge_user', function (Blueprint $table) {
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('badge_user');
    }
}
