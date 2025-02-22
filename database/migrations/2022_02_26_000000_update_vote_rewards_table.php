<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('vote_reward_server')) {
            return; // Everything is good
        }

        $rewards = DB::table('vote_rewards')->get();
        $servers = DB::table('servers')->pluck('id');

        try {
            Schema::table('vote_rewards', function (Blueprint $table) {
                $table->dropColumn('server_id');
            });
        } catch (Throwable $e) {
            $class = require __DIR__.'/2019_12_31_100000_create_vote_rewards_table.php';

            $migration = new $class();

            $migration->down();
            $migration->up();

            return;
        }

        Schema::create('vote_reward_server', function (Blueprint $table) {
            $table->unsignedInteger('reward_id');
            $table->unsignedInteger('server_id');

            $table->foreign('reward_id')->references('id')->on('vote_rewards')->cascadeOnDelete();
            $table->foreign('server_id')->references('id')->on('servers')->cascadeOnDelete();

            $table->unique(['reward_id', 'server_id']);
        });

        foreach ($rewards as $reward) {
            if ($servers->contains($reward->server_id)) {
                DB::table('vote_reward_server')->insert([
                    'reward_id' => $reward->id,
                    'server_id' => $reward->server_id,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
