<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy
{

    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }
}
