<?php

namespace App\Policies;

use App\Models\GameUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GameUserPolicy
{
    public function workWith(User $user, GameUser $gameUser): bool
    {
        return $gameUser->user->is($user);
    }
}
