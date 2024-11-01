<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // public function posts(): BelongsToMany
    // {
    //     return $this->belongsToMany(Post::class);
    // }

    # MANY TO MANY POLYMORPHIC RELATIONSHIP
    public function taggables(): MorphToMany
    {
        # arg1: Related Model
        # arg2: name of the polymorphic relationship
        return $this->morphToMany(Taggable::class, 'taggable');
    }
}
