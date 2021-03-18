<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_videos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('path',400);
            $table->string('title',400);
            $table->string('description',2000);
            $table->string('thumbnail_path',400);
            $table->tinyInteger('status');
            $table->integer('view');
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
        Schema::dropIfExists('api_videos');
    }
}
