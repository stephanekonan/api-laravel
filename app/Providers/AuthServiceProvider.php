<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Like;
use App\Models\Post;
use App\Models\Event;
use App\Models\Comment;
use App\Policies\LikePolicy;
use App\Policies\PostPolicy;
use App\Policies\EventPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Event::class => EventPolicy::class,
        Like::class => LikePolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
