<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['post_id', 'user_id', 'event_id', 'content'];

    protected static function booted()
    {
        static::creating(function ($comment) {
            $comment->uid = (string) Str::uuid();
            $comment->user_id = Auth::id();
        });
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
