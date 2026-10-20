<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\GameUser;

class Platform extends Model
{
    protected $fillable= ['name', 'igdb_id', 'icon'];

    public function userGames(): BelongsToMany
    {
        return $this->belongsToMany(GameUser::class);
    }
}
