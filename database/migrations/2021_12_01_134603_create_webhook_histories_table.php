<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_webhook_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('webhook_reward_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('webhook_reward_id')->references('id')->on('vote_webhook_rewards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_webhook_histories');
    }
}
