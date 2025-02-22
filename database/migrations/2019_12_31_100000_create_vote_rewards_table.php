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
        Schema::create('vote_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('chances');
            $table->decimal('money')->default(0);
            $table->text('commands')->nullable();
            $table->boolean('need_online')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('vote_reward_server', function (Blueprint $table) {
            $table->unsignedInteger('reward_id');
            $table->unsignedInteger('server_id');

            $table->foreign('reward_id')->references('id')->on('vote_rewards')->cascadeOnDelete();
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            $table->unique(['reward_id', 'server_id']);
        });

        Schema::create('vote_reward_site', function (Blueprint $table) {
            $table->unsignedInteger('reward_id');
            $table->unsignedInteger('site_id');

            $table->foreign('reward_id')->references('id')->on('vote_rewards')->cascadeOnDelete();
            $table->foreign('site_id')->references('id')->on('vote_sites')->cascadeOnDelete();

            $table->unique(['reward_id', 'site_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_rewards');
        Schema::dropIfExists('vote_reward_server');
        Schema::dropIfExists('vote_reward_site');
    }
};
