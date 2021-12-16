<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_webhook_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vote_reward_id');
            $table->string('webhook');
            $table->string('site')->default('smv');
            $table->integer('limit')->default(0);
            $table->timestamps();

            $table->foreign('vote_reward_id')->references('id')->on('vote_rewards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_webhook_rewards');
    }
}
