<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Server;
use Azuriom\Models\Traits\HasTablePrefix;
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
     * Return reward model.
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
        $rewards = self::select('vote_webhook_rewards.*')
            ->join('vote_rewards', 'vote_rewards.id', '=', 'vote_reward_id')
            ->where('vote_webhook_rewards.webhook', $type)->where('vote_rewards.is_enabled', true)->get();

        $total = $rewards->map(function ($value) {
            return $value->reward;
        })->sum('chances');

        $random = random_int(0, $total);

        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += $reward->reward->chances;

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
}
