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


## ONE TO MANY RELATIONSHIP

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

## MANY TO MANY RELATIONSHIP

Multiple records in the first table associated with multiple records in the second table. 

Many to many relationship is one of the more complex relationships. 

Requires an additional table to store the relationshiops between two tables. This additional table is called a **pivot table**, which plays a crucial role in the Many to Many relationship.

The **pivot table** allows us to store the relationships between the two tables and query the data easily.

Real life example: Relationship between Students and Classes. Each student can be enrolled in multiple classes and each class can have multiple students. 

Another example: Each post can have multiple tags and each tags can be associated with multiple posts.


In this example, we'll use Tags and Posts. 

CLI:
```
> php artisan make:model Tag -ms --resource
> php artisan make:model Tag -m
```

To create a **pivot table**, the name convention should be a combiantion of the singular names of the two tables being related separated by an underscore and an alphabetic order.

***No need a Model***.

ON CLI
```
> php artisan make:migration create_post_tag_table
```

On `app/Models/Post`:
```
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
...
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
...
```

On `app/Models/Tag`
```
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
...
public function posts(): BelongsToMany
{
    return $this->belongsToMany(Post::class);
}
...
```

On Tinker testing:
```
# Insert 5 or more
Tag::create([
    'name' => 'Tech',
    'slug' => 'tech'
]);

Tag::all();
```

### attach()

The attach() method is used to insert data into the pivot table, where it will associate the post with tags with the given IDS.

This method does not returns any value, but insert the data into the pivot table.

```
# Insert ti Pivot table

$tagIds = [1,2,3];

$post = Post::find(1);


# returns as NULL.
$post->tags()->attach($tagIds);
```

### retrieve fro Pivot Table

```
$post = Post::find(1);

$post->tags()->attach($tagIds);

$post->tags()->get();

foreach($post->tags as $tag){
    echo "Tag: " . $tag->name . "\n";
}
```

### detach()

The detach() method is used to REMOVE data from a Many-to-Many relationship.

```
# detach all
$post->tags()->detach();

# detach a specific one
$post->tags()->detach(1);

# detach an array of list
$post->tags()->detach([1,2,3]);
```

### update- updateExistingPivot() Many-to-Many

The `updateExistingPivot()` method allows you to update a single record on the intermediate table.

```
Tag::create([
    'name' => 'UPDATED',
    'slug' => 'update'
]);

# Actualizar el ID del tags a por el otro tag.
# args1: ID relation
# args2: An array you want to update. Se cambio el id original a por el nuevo.
$post->tags()->updateExistingPivot(1, [
    'tag_id' => 4
]);
```

INVERSE
```

# Actualizar los POSTS dentro del Tag

$postIDs = [6,7,8];
$tag = Tag::find(1);

$tag->posts()->attach($postIDs);
```

```
foreach($tag->posts as $post){
    echo "Posts: " . $post->title . "\n";
}
```

## EAGER LOADING

Eager loading is a technique for loading related data up front instead of loading it lazy when it is needed.

Eager loading loads all the necessary data in advance.

Eager loading is a powerful tool for improving performance in Laravel.

### Why useful? 

Eager loading is useful because it helps you to reduce the number of queries that are executed on your DB by loading all the necessary data upfront with eager loading. *When you're not using eager loading, it will pretty much execute a separate query for each related model that you access*.

When you use eager loading, laravel will execute a single query that loads all the related data upfront.

Lets have the difference:
```
# This is Lazy Load.
Post::all();

# Eager Loading
Post::with('tags')->get();

```

