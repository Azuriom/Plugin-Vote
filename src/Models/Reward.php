<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\User;
use Azuriom\Models\Server;
use Illuminate\Support\HtmlString;
use Azuriom\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Azuriom\Models\Traits\HasTablePrefix;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property string $name
 * @property int $chances
 * @property int|null $money
 * @property bool $need_online
 * @property array $commands
 * @property bool $is_enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Illuminate\Support\Collection|\Azuriom\Plugin\Vote\Models\Vote[] $votes
 * @property \Illuminate\Support\Collection|\Azuriom\Models\Server[] $servers
 *
 * @method static \Illuminate\Database\Eloquent\Builder enabled()
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
        'name', 'image', 'chances', 'money', 'commands', 'need_online', 'is_enabled',
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

    public function imageUrl()
    {
        return $this->image !== null ? image_url($this->image) : "";
    }

    public function getNameAttribute(string $value)
    {
        $image = $this->image !== null ? '<img src="'.$this->imageUrl().'" width="30px" /> ' : '';

        return new HtmlString($image.e($value));
    }

    public function rawName()
    {
        return $this->getRawOriginal('name');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'vote_reward_site');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class, 'vote_reward_server');
    }

    public function giveTo(User $user)
    {
        if ($this->money > 0) {
            $user->addMoney($this->money);
        }

        $commands = $this->commands ?? [];

        if ($globalCommands = setting('vote.commands')) {
            $commands = array_merge($commands, json_decode($globalCommands));
        }

        if (empty($commands)) {
            return;
        }

        $commands = array_map(function (string $command) {
            return str_replace('{reward}', $this->name, $command);
        }, $commands);

        foreach ($this->servers as $server) {
            $server->bridge()->sendCommands($commands, $user, $this->need_online);
        }
    }

    /**
     * Scope a query to only include enabled vote rewards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled(Builder $query)
    {
        return $query->where('is_enabled', true);
    }
}
