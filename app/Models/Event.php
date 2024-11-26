<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'url_image',
        'title',
        'description',
        'date',
        'lieu',
    ];

    protected static function booted()
    {
        static::creating(function ($event) {
            $event->uid = (string) Str::uuid();
            $event->user_id = Auth::id();
        });
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
