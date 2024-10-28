# RELATIONSHIP ORM/QUERY

Relantionship refer to the connections between different entities in a database.

Disadvantage: It can be complex to set up, especially for large projects with multiple tables. It can lead to slower database operations, especially when dealing with large datasets. But setting up relationship correctly is crucial in big projects to ensure data consistency and accuracy. Properly establishing relatinships ensure that data is stored and managed correctly. 

## ONE TO ONE RELATIONSHIP

The one-to-one relationship is used when we need to associate two tables with a unique relationship. 

Case use: A table with sensitive information that we want to keep separate from the main table. Example, a user table that contain information such as a name and email, password and so on; but we want to keep the users at risk: city zip code and all those other information in a separate table for security reason. Simply create a Contact table. 

On CLI, create a Contact Model with this migration:
```
> php artisan make:model Contact -m
```

**BY DEFAULT, ALL RELATIONSHIP ARE RELATED WITH `id`**.

ON `app/Models/User.php`
```
use Illuminate\Database\Eloquent\Relations\HasOne;

public function contact(): HasOne
{
    # You can customize the foreign key, local key
    // return $this->hasOne(Contact::class, 'user_id', 'local_key');

    return $this->hasOne(Contact::class);
}
```

On `app/Models/Contact.php`

**Each contact belongs to ONE user.**
```
use Illuminate\Database\Eloquent\Relations\BelongsTo;

...
public function user(): BelongsTo
{
    # Has 3 args. 
    # arg1: Model Class
    # arg2: Foreign key
    # arg3: Local Key
    $this->belongsTo(User::class);
}
...
```

Examples for Testing (On tinker): 

1. Example 1

```
User::create([
    'name' => 'Coding with PHp',
    'email' => 'user1@user1.com',
    'password' => 'password'
]);

Contact::create([
    'user_id' => 21,
    'address' => 'Vallejo',
    'city' => 'Mexico',
    'number' => 24,
    'zip_code' => 11111
]);
```

2. Example 2

```
$user = User::create([
            'name' => 'User 2',
            'email' => 'user2@user2.com',
            'password' => 'password', 
            'balance' => 35234
        ]);

# DONT USE 'user_id'
$user->contact()->create([
    'address' => 'Cofradia 4',
    'city' => 'Edo de mex',
    'number' => 34,
    'zip_code' => 32452
]);
```

Laravel offers `with()` methods, which is used to ***eager loads*** data in a related model. 

```
User::with('contact')->find(21);

User::with('contact')->all();

User::with('contact')->latest()->get();
```

Others example
```
$user = User::find(21);

# It called dynamic property accessing (address)
# If the user doesn't have a contact associated with item, 
# the contact property will be null.
$user->contact->address;
```


## ONE TO MANY

Real life scenario: the user can create multiple blog posts. Each blogs belongs to a specific user. 

`app/Models/User`
```
use Illuminate\Database\Eloquent\Relations\HasMany;
...
# ONE TO MANY
# Make the name in plural. 
public function posts(): HasMany
{
    # arg1: Related Model
    # arg2: foreign key, example: 'user_id',
    # arg3: local key, example: 'id'
    return $this->hasMany(Post::class);
}
...
```

`app/Models/Post`
```
use Illuminate\Database\Eloquent\Relations\BelongsTo;
...
# MANY TO ONE
# Use singular name
public function user(): BelongsTo
{
    # args1: Related MOdel
    # args2: Search Foreign Key.
    return $this->belongsTo(User::class);
}
...
```

Testing with Tinker
```
$post = Post::find(1);

$user = $post->user;

# access indivual attributes
$user = $post->user->name;
```

Retrieves many posts by a specific User
```
$user = User::find(1);

$posts = $user->posts;
```

Loop for each posts.
```
foreach($user->posts  as $post){
    echo "Title:" . $post->title;
}
```
