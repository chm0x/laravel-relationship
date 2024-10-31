<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'imageable_id',
        'imageable_type'
    ];
    # The image model is used to represent an image that can
    # belong to EITHER an User or a (mode)Post.
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
