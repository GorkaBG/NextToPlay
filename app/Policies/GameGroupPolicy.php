<?php

namespace App\Policies;

use App\Models\GameGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GameGroupPolicy
{
    public function workWith(User $user, GameGroup $gamegroup): bool
    {
        return $gamegroup->user->is($user);
    }
}
