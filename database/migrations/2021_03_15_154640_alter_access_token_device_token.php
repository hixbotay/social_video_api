<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccessTokenDeviceToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('api_personal_access_tokens', function (Blueprint $table) {
            $table->string('device_token', 2048)->default('');
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
        Schema::table('api_personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('device_token');
        });
    }
}
