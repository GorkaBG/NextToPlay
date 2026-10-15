<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class GameGroup extends Model
{
    protected $fillable = [
        'user_id',
        'groupname',
        'color',
        'icon',
        'order',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
