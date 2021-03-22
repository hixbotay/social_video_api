<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserMetaMore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_user_metas', function (Blueprint $table) {
            //
            $table->string('birthday',10);
            $table->integer('number_follow')->default(0);
            $table->integer('number_follow_me')->default(0);
            $table->integer('number_friend')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_user_metas', function (Blueprint $table) {
            //
            $table->dropColumn(['birthday']);
        });
    }
}
