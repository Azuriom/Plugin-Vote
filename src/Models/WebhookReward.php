<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Server;
use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $webhook
 * @property string $site
 * @property int $limit
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Reward $reward
 *
 * @method static Builder enabled()
 */
class WebhookReward extends Model
{
    use HasTablePrefix;
    use HasFactory;

    /**
     * The table prefix associated with the model.
     *
     * @var string
     */
    protected $prefix = 'vote_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site', 'webhook', 'limit', 'vote_reward_id',
    ];

    /**
     * Return reward model
     *
     * @return BelongsTo
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class, 'vote_reward_id');
    }

    /**
     * Return a reward.
     *
     * @param  string  $type
     * @param $user
     * @return WebhookReward|null
     *
     * @throws Exception
     */
    public static function getRandomReward(string $type, $user): ?WebhookReward
    {
        $rewards = self::select('vote_webhook_rewards')
            ->join('vote_rewards', 'vote_rewards.id', '=', 'vote_reward_id')
            ->where('vote_rewards.webhook', $type)->where('vote_rewards.is_enabled', true)->get();

        $total = $rewards->sum('rewards.chances');
        $random = random_int(0, $total);

        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += $reward->chances;

            if ($sum >= $random) {
                $historyCount = WebhookHistory::where('webhook_reward_id', $reward->id)->where('name', $user)->count();

                if ($reward->limit !== 0 && $historyCount >=
                    $reward->limit) {
                    continue;
                }

                return $reward;
            }
        }

        return null;
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function giveTo($userName)
    {
        if ($this->reward->money > 0) {
            $user = User::where('name', $userName)->first();

            if (isset($user)) {
                $user->addMoney($this->reward->money);
                $user->save();
            }
        }

        $commands = $this->reward->commands ?? [];

        $commands = array_map(function ($el) {
            return str_replace('{reward}', $this->reward->name, $el);
        }, $commands);

        if ($this->reward->server !== null && ! empty($commands)) {
            $this->reward->server->bridge()->executeCommands($commands, $userName, $this->reward->need_online);
        }
    }
}
