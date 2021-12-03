<?php

namespace Azuriom\Plugin\Vote\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $webhook_reward_id
 * @property WebhookReward $webhook
 */
class WebhookHistory extends Model
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
        'webhook_reward_id', 'name',
    ];

    public function webhook(){
        return $this->belongsTo(WebhookReward::class, 'webhook_reward_id');
    }

}
