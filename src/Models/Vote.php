<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\Searchable;
use Azuriom\Models\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $user_id
 * @property int $site_id
 * @property int $reward_id
 * @property bool $is_enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\User $user
 * @property \Azuriom\Plugin\Vote\Models\Site|null $site
 * @property \Azuriom\Plugin\Vote\Models\Reward|null $reward
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
 */
class Vote extends Model
{
    use HasTablePrefix;
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
        'user_id', 'reward_id',
    ];

    /**
     * The attributes that can be used for search.
     *
     * @var array<int, string>
     */
    protected array $searchable = [
        'site.*', 'reward.*', 'user.name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public static function getTopVoters(DateTime $from, ?DateTime $to = null)
    {
        $votes = static::getRawTopVoters($from, $to);

        $users = User::findMany($votes->pluck('user_id'))->keyBy('id');

        return $votes->mapWithKeys(function ($vote, $position) use ($users) {
            return [
                $position + 1 => (object) [
                    'user' => $users->get($vote->user_id),
                    'votes' => $vote->count,
                    'position' => $position + 1,
                ],
            ];
        });
    }

    public static function getRawTopVoters(DateTime $from, ?DateTime $to = null, ?int $max = null)
    {
        return self::select(['user_id', DB::raw('count(*) as count')])
            ->whereBetween('created_at', [$from, $to ?? now()])
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->take($max ?? setting('vote.top-players-count', 10))
            ->get();
    }
}
