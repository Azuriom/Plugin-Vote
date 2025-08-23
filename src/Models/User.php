<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\User as BaseUser;

/**
 * @property int $id
 * @property string $name
 * @property ?string $game_id
 * @property \Illuminate\Support\Collection|\Azuriom\Plugin\Vote\Models\Vote[] $votes
 */
class User extends BaseUser
{
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
