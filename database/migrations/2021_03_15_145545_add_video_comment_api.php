<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoCommentApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_video_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('video_id');
            $table->bigInteger('parent_id');
            $table->string('comments',1000);
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
        Schema::dropIfExists('api_video_comments');
    }
}
