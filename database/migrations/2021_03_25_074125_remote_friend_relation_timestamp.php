<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoteFriendRelationTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_friend_relations', function (Blueprint $table) {
            //
			$table->boolean('is_friend')->default(false)->change();
			$table->boolean('is_follow')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_friend_relations', function (Blueprint $table) {
            //
        });
    }
}
