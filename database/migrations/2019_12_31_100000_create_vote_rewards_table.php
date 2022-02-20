<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('server_id');
            $table->unsignedDecimal('chances');
            $table->unsignedDecimal('money')->default(0);
            $table->text('commands')->nullable();
            $table->boolean('need_online')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('vote_reward_site', function (Blueprint $table) {
            $table->unsignedInteger('reward_id');
            $table->unsignedInteger('site_id');

            $table->foreign('reward_id')->references('id')->on('vote_rewards')->onDelete('cascade');
            $table->foreign('site_id')->references('id')->on('vote_sites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_rewards');
        Schema::dropIfExists('vote_reward_site');
    }
};
