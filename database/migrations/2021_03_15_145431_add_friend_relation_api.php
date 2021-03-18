<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFriendRelationApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_friend_relations', function (Blueprint $table) {
            $table->id();
            $table->integer('from_user_id');
            $table->integer('to_user_id');
            $table->boolean('is_friend');
            $table->boolean('is_follow');
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
        Schema::dropIfExists('api_friend_relations');
    }
}
