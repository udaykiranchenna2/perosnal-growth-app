<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('generated_tweets', function (Blueprint $table) {
            $table->foreignId('x_community_id')->nullable()->constrained('x_communities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_tweets', function (Blueprint $table) {
            $table->dropForeign(['x_community_id']);
            $table->dropColumn('x_community_id');
        });
    }
};
