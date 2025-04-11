<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tweet_contexts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('x_post_settings_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('context');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tweet_contexts');
    }
}; 