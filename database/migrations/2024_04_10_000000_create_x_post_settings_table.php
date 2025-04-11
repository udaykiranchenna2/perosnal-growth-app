<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('x_post_settings', function (Blueprint $table) {
            $table->id();
            $table->string('profile_name');
            $table->text('about_me');
            $table->text('personality');
            $table->integer('max_tweet_length')->default(280);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('x_post_settings');
    }
}; 