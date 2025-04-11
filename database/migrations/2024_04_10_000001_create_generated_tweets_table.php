<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('generated_tweets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('x_post_settings_id')->constrained('x_post_settings')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->string('tweet_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_tweets');
    }
}; 