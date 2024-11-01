<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'description'
    ];

    public function comments(): MorphMany
    {
        # arg1: Related Model, Comment
        # arg2: Name of the Morph field, exampel: commentable
        return $this->morphMany(Comment::class,'commentable');
    }
}
