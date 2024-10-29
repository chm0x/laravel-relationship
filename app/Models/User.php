<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    # ONE TO ONE RELATIONSHIP
    # singular name.
    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    # ONE TO MANY
    # Make the name in plural. 
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }


    # company
    public function companyPhoneNumber(): HasOneThrough
    {
        # arg1: Model Class
        # arg2: Join to Model Class
        # arg3: Foreign Key of the first model class
        # arg4: FOreign key of the second model class
        # arg5: Local Key to joining (the first class)
        # arg6: Local key to joining (the second class)
        // return $this->hasOneThrough(PhoneNumber::class, 
        //         Company::class,
        //         'user_id',
        //         'company_id',
        //         'id',
        //         'id'
        //     );
        return $this->hasOneThrough(PhoneNumber::class,Company::class );
    }
}
