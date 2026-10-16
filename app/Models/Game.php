<?php

namespace App\Models;

use App\Models\Developer;
use App\Models\GameUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    protected $fillable = [
        'igdb_id',
        'name',
        'cover',
        'release_date',
        'total_rating',
        'total_rating_count'
    ];

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(Developer::class);
    }
}
