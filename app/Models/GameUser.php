<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Platform;
use App\Models\User;
use App\Models\Game;
use App\Models\GameGroup;

class GameUser extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'game_group_id',
        'custom_title',
        'custom_cover',
        'finished',
        'position',
    ];

    protected $attributes = [
        'finished' => false,
        'position' =>1,
    ];

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'game_user_platform');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function game():BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
    public function group(): BelongsTo
    {
        return $this->belongsTo(GameGroup::class);
    }
}