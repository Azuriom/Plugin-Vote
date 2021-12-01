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
            $table->string('name');
            $table->string('webhook');
            $table->string('site')->default('smv');
            $table->unsignedInteger('server_id');
            $table->unsignedInteger('chances');
            $table->unsignedDecimal('money')->default(0);
            $table->text('commands')->nullable();
            $table->boolean('need_online')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
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
