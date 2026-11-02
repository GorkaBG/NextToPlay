<?php

namespace App\Models;

use App\Models\Developer;
use App\Models\GameUser;
use App\Models\Platform;

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
        'total_rating_count',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'datetime', 
            'duration' => 'array',           
        ];
    }

    public function developers(): BelongsToMany
    {
        return $this->belongsToMany(Developer::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class);
    }

}
