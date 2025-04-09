<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sticky_notes', function (Blueprint $table) {
            $table->string('shared_url')->nullable()->after('color');
        });
    }

    public function down()
    {
        Schema::table('sticky_notes', function (Blueprint $table) {
            $table->dropColumn('shared_url');
        });
    }
}; 