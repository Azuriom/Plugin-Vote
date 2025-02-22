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
        Schema::table('vote_rewards', function (Blueprint $table) {
            $table->boolean('single_server')->default(false)->after('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vote_rewards', function (Blueprint $table) {
            $table->dropColumn('single_server');
        });
    }
};
