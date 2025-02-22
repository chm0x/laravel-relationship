<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    
    # MANY TO ONE
    # Use singular name
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class);
    // }

    # MANY TO MANY
    // public function tags(): BelongsToMany
    // {
    //     # args1: Related Model
    //     # args2(optional): Pivot table name. Laravel will automatically generate a pivot
    //     # table name by combining the singular names of two tables 
    //     # in alphabetic order.
    //     // return $this->belongsToMany(Tag::class, 'post_tag');
    //     return $this->belongsToMany(Tag::class);
    // }

    // # ONE TO ONE MORPHY
    // public function image(): MorphOne
    // {
    //     return $this->morphOne(Image::class, 'imageable');
    // }

    // # ONE TO MANY POLYMORPHIC
    // public function comments(): MorphMany
    // {
    //     return $this->morphMany(Comment::class, 'commentable');
    // }

    # MANY TO MANY POLYMORPHIC RELATIONSHIP

    /**
     * This method allows the model to be associated with multiple
     * instance of the Tag and the other way around as well.
     */
    public function tags(): MorphToMany
    {
        # arg1: Related Model
        # arg2: name of the polymorphic relation
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
