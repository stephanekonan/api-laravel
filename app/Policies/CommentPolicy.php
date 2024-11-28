<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Comment $comment): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'client';
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' || $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' || $user->id === $comment->user_id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->role === 'admin';
    }
}
