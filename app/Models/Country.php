<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code'
    ];

    # name convention: name of the target model in plural form, 
    # This relationship allows the country model to Retrieve 
    # all posts associated with the related user Models.
    public function posts(): HasManyThrough
    {
        # IT REQUIRES TWO ARGS. 
        # And has 6 args.
        # arg1: Final Model
        # arg2: Intermediate Model
        # arg3: Foreign Key on User's table (Through): country_id.
        # arg4: Foreign Key on Post Table (intermediarie): user_id
        # arg5: Local keys on both country and users: id
        # arg6: Local keys on both: id
        return $this->hasManyThrough(Post::class, User::class);
    }
}
