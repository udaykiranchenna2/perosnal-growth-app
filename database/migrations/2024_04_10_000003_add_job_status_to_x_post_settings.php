<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('x_post_settings', function (Blueprint $table) {
            $table->boolean('x_post_job_queued')->default(false);
        });
    }

    public function down()
    {
        Schema::table('x_post_settings', function (Blueprint $table) {
            $table->dropColumn('x_post_job_queued');
        });
    }
}; 