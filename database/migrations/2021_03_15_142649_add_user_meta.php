<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_user_metas', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('photo_path',255);
            $table->string('verify_photo',1000);
            $table->tinyInteger('is_verify');
            $table->string('fb_id',100)->nullable();
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
        Schema::dropIfExists('api_user_metas');
    }
}
