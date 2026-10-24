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
use Illuminate\Support\Str;

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
        'vote_delay' => 'integer',
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
        $rewards = $this->rewards()->enabled()->where('chances', '>', 0)->get();

        // Chances have two decimals: multiply by 100 to avoid float rounding issues
        $total = (int) round($rewards->sum('chances') * 100);

        if ($total <= 0) {
            return null;
        }

        $random = random_int(1, $total);
        $sum = 0;

        foreach ($rewards as $reward) {
            $sum += (int) round($reward->chances * 100);

            if ($sum >= $random) {
                return $reward;
            }
        }

        return $rewards->last();
    }

    public function getNextVoteTime(User $user, Request|string|null $ip = null): ?Carbon
    {
        return $this->calculateNextVoteTime(
            self::allLastVoteTimes($user)[$this->id] ?? null,
            $ip,
        );
    }

    public function getFreshNextVoteTime(User $user, Request|string|null $ip = null): ?Carbon
    {
        $lastVoteTime = $this->votes()
            ->where('user_id', $user->id)
            ->max('created_at');

        return $this->calculateNextVoteTime(
            $lastVoteTime !== null ? Carbon::parse($lastVoteTime) : null, $ip,
        );
    }

    private function calculateNextVoteTime(?Carbon $lastVoteTime, Request|string|null $ip): ?Carbon
    {
        $voteTime = $this->vote_reset_at !== null
            ? now()->previous($this->vote_reset_at)
            : now()->subMinutes($this->vote_delay);

        if ($lastVoteTime?->isAfter($voteTime)) {
            return $this->vote_reset_at !== null
                ? $voteTime->addDay()
                : $lastVoteTime->addMinutes($this->vote_delay);
        }

        if ($ip === null) {
            return null;
        }

        $ip = $ip instanceof Request ? $ip->ip() : $ip;
        $nextVoteTime = Cache::get('votes.site.'.$this->id.'.'.$ip);

        if (! $nextVoteTime?->isFuture()) {
            return null;
        }

        return $nextVoteTime;
    }

    /**
     * Scope a query to only include enabled vote sites.
     */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('is_enabled', true);
    }

    /**
     * Get the host of a site URL, without the "www." prefix, or null if the URL has none.
     */
    public static function hostFromUrl(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host)) {
            return null;
        }

        $host = strtolower($host);

        return Str::startsWith($host, 'www.') ? substr($host, 4) : $host;
    }

    public static function allLastVoteTimes(User $user): array
    {
        $cache = once(fn () => collect());

        if ($cache->has($user->id)) {
            return $cache->get($user->id);
        }

        $lastVoteTimes = static::withMax(
            ['votes as last_voted_at' => fn ($q) => $q->where('user_id', $user->id)],
            'created_at'
        )
            ->get()
            ->pluck('last_voted_at', 'id')
            ->map(fn (?string $date) => $date !== null ? Carbon::parse($date) : null)
            ->all();

        $cache->put($user->id, $lastVoteTimes);

        return $lastVoteTimes;
    }
}
