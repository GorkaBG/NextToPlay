<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\GameUser;

class GameGroup extends Model
{
    /** @use HasFactory<\Database\Factories\GameGroupFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'groupname',
        'color',
        'icon',
        'order',
    ];
    protected $attributes = [
        'order' => 1
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function userGames(): HasMany
    {
        return $this->hasMany(UserGame::class);
    }
}
