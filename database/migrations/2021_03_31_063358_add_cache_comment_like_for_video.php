<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCacheCommentLikeForVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_videos', function (Blueprint $table) {
            //
            $table->integer('number_comment')->default(0);
            $table->integer('number_like')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_videos', function (Blueprint $table) {
            $table->dropColumn('number_comment', 'number_like');
        });
    }
}
