<?php

namespace Azuriom\Plugin\Vote\Models;

use Illuminate\Database\Eloquent\Model;
use Azuriom\Models\Traits\HasTablePrefix;

class Pingback extends Model
{
    use HasTablePrefix;

    /**
     * The table prefix associated with the model.
     *
     * @var string
     */
    protected $prefix = 'vote_';

    protected $fillable = ['domain', 'ip'];
}