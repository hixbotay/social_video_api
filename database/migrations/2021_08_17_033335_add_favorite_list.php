<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFavoriteList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_video_favorite', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('name',255);
            $table->tinyInteger('ordering')->default(0);
            $table->mediumText('list');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('api_video_favorite');
    }
}
