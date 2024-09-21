<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\Traits\Searchable;
use Azuriom\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $name
 * @property string $url
 * @property int $vote_delay
 * @property string|null $verification_key
 * @property bool $has_verification
 * @property bool $is_enabled
 * @property \Carbon\Carbon|null $vote_reset_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Illuminate\Support\Collection|\Azuriom\Plugin\Vote\Models\Reward[] $rewards
 * @property \Illuminate\Support\Collection|\Azuriom\Plugin\Vote\Models\Vote[] $votes
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
 */
class Site extends Model
{
    use HasTablePrefix;
    use Loggable;
    use Searchable;

    /**
     * The table prefix associated with the model.
     */
    protected string $prefix = 'vote_';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'url', 'vote_delay', 'verification_key', 'has_verification', 'is_enabled', 'vote_reset_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'need_online' => 'boolean',
        'has_verification' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    /**
     * The attributes that can be used for search.
     *
     * @var array<int, string>
     */
    protected array $searchable = [
        'name',
    ];

    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'vote_reward_site');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getRandomReward(): ?Reward
    {
        $total = $this->rewards->sum('chances');

        if ($total < 0.01) {
            return null;
        }

        // Multiply to support decimal chances
        $random = random_int(1, $total * 1000);
        $sum = 0;

        foreach ($this->rewards as $reward) {
            $sum += $reward->chances * 1000;

            if ($sum >= $random) {
                return $reward;
            }
        }

        return $this->rewards->first();
    }

    public function getNextVoteTime(User $user, Request|string $ip): ?Carbon
    {
        if ($ip instanceof Request) {
            $ip = $ip->ip();
        }

        $voteTime = $this->vote_reset_at !== null
            ? now()->previous($this->vote_reset_at)
            : now()->subMinutes($this->vote_delay);

        $lastVoteTime = $this->votes()
            ->where('user_id', $user->id)
            ->where('created_at', '>', $voteTime)
            ->latest()
            ->value('created_at');

        if ($lastVoteTime !== null) {
            return $this->vote_reset_at !== null
                ? $voteTime->addDay()
                : $lastVoteTime->addMinutes($this->vote_delay);
        }

        $nextVoteTimeForIp = Cache::get('votes.site.'.$this->id.'.'.$ip);

        if ($nextVoteTimeForIp === null || $nextVoteTimeForIp->isPast()) {
            return null;
        }

        return $nextVoteTimeForIp;
    }

    /**
     * Scope a query to only include enabled vote sites.
     */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }
}
