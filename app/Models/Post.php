<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    
    # MANY TO ONE
    # Use singular name
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    # MANY TO MANY
    public function tags(): BelongsToMany
    {
        # args1: Related Model
        # args2(optional): Pivot table name. Laravel will automatically generate a pivot
        # table name by combining the singular names of two tables 
        # in alphabetic order.
        // return $this->belongsToMany(Tag::class, 'post_tag');
        return $this->belongsToMany(Tag::class);
    }
}
