<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Server;
use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property int $server_id
 * @property int $chances
 * @property int|null $money
 * @property bool $need_online
 * @property array $commands
 * @property bool $is_enabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Server|null $server
 * @property Collection|Vote[] $votes
 * @property Collection|WebhookReward[] $webhook
 *
 * @method static Builder enabled()
 */
class Reward extends Model
{
    use HasTablePrefix;
    use Loggable;

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
        'name', 'server_id', 'chances', 'money', 'commands', 'need_online', 'is_enabled',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'commands' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'vote_reward_site');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function webhook()
    {
        return $this->hasOne(WebhookReward::class, 'vote_reward_id');
    }

    public function giveTo($username, User $user = null)
    {
        if ($this->money > 0 && isset($username)) {

            $user = $user ?? User::where('name', $username)->first();

            if (isset($user)) {
                $user->addMoney($this->money);
                $user->save();
            }
        }

        $commands = $this->commands ?? [];

        if ($globalCommands = setting('vote.commands')) {
            $commands = array_merge($commands, json_decode($globalCommands));
        }

        $commands = array_map(function ($el) {
            return str_replace('{reward}', $this->name, $el);
        }, $commands);

        if ($this->server !== null && !empty($commands)) {
            $this->server->bridge()->executeCommands($commands, $username, $this->need_online);
        }
    }

    /**
     * Scope a query to only include enabled vote rewards.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeEnabled(Builder $query)
    {
        return $query->where('is_enabled', true);
    }
}
