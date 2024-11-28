<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LikePolicy
{

    public function like(User $user, $likeable): bool
    {
        return in_array($user->role, ['client', 'admin']);
    }

    public function unlike(User $user, $likeable): bool
    {
        return $likeable->likes->where('user_id', $user->id)->count() > 0;
    }

}
