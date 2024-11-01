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


## HAS ONE THROUGH RELATIONSHIP

The 'Has One Through' Relationship allows you to define a direct association between two Models through a **third intermediarie** model. 

This relationship is useful when you need to access the data of the intermediate model to retrieve the data of the second model.

When? When you have relationship between two models that is not direct but has a model in between that connects them.

Examples: user that work on a company, and a company has a phone number. The phone number belongs to the company, but we need to go through the user to get it. This relationship comes in handy.

Examples:
```
> php artisan make:model Company -m
> php artisan make:model PhoneNumber -m
```
`app/Models/User.php`
```
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
...
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
...
```

Testing on Tinker
```
Company::create([
    'name' => 'Apple',
    'user_id' => 5,
    
]);

PhoneNumber::create([
    'number' => '1122445577',
    'company_id' => 1,
]);

# To access phone number from a user;
$user = User::find(5);

$user->companyPhoneNumber()->get();

$user = User::find(5)->companyPhoneNumber()->get();

$user = User::with('companyPhoneNumber')
            ->get();
```


## HAS ONE OF MANY (THROUGH)  RELATIONSHIP

In real life; An user has a job portal where the user can apply for multiple jobs and wants to either retrieve the first or last job. That is where the "Has one of Many" relationship comes in handy. 

```
> php artisan make:model Job -m 
```

On `app/Models/User.php`
```
use Illuminate\Database\Eloquent\Relations\HasOne;
...
public function latestJob(): HasOne
{
    return $this->hasOne(Job::class)->latestOfMany();
}
public function oldestJob(): HasOne
{
    return $this->hasOne(Job::class)->oldestOfMany();
}
...
```

On testing in Tinker:
```
Job::create([
    'title' => 'Junior Web Dev',
    'description' => '1000 per month',
    'user_id' => 5,
    'is_active' => true
]);

User::find(5)->latestJob()->get();

User::find(5)->oldestJob()->get();

User::find(5)->oldestJob()->toSql();
```

## HAS MANY THROUGH RELATIONSHIP

This relationship is useful when you want to retrieve data through a Many-to-Many relationship where the intermediate table has an additional column beyond the foreign keys.

Real life scenario: A social network app where users can create posts and each user is associated with a country. In this scenario, has 3 models: User, Post and Country. 

The country modelis associated with many users, and each user is associated with many posts. That Has-To-Many-Through relationship comes in handy. *It where we want to retrieve all the posts associated with a particular country without defining a relationship between both*.

```
> php artisan make:model Country -m
```

ON `app/Models/Country.php`
```
...
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
...
```

Tinker:
```
Country::create([
    'name' => 'Mexico',
    'code' => 'MX'
]);

User::where('id', 5)->update([
    'country_id' => 2
]);

# User with his POsts
User::with('posts')
    ->where('id', 5)
    ->get();

# Country
$country = Country::find(2);

# Without parenthesis
$country->posts;
```

# POLYMORPHIC RELATIONSHIPS

It helps you to reduce data redundancy as we don't have to create a separate table for each realtionship.
They also provide a lot more flexibility as they allow us to create complex relationships between models. Another advantage of using polymorphic relationships in Laravel is that they make it easier to manage relationships between models. Instead of having to deal with multiple tables, we can make one single table, which makes the code much cleaner and easier to maintain. 

Disadvantage: Complexity, complex to implement and not be suitable for all projects. Example that may not be suitable: It where there are only a few relationships between tables. The second disadvantage is performance" overhead. It can have a perfomance overhead as they require additional queries to retrieve related data.


## ONE TO ONE POLYMORPHIC

This relationship allows a model to belong to more than one other model on a single association.

Reduces the code duplication and it will improve the maintanability.

Real life example: Messaging system. A message model that can belong to either a User or a Group. The User and Group models would be associated with a message  model through a polymorphic. Defining such relationship is useful when you have multiple models that share a common relationship. It also allows you to create a single relationship that can be used across multiple models. 

Example: Storing images that can belong to either an User or a Post.

```
> php artisan make:model Image -m
```

On `app/Models/Image`
```
use Illuminate\Database\Eloquent\Relations\MorphTo;
...
# The image model is used to represent an image that can
# belong to EITHER an User or a (mode)Post.
public function imagenable(): MorphTo
{
    return $this->morphTo();
}
...
```

On `app/Models/User`
```
use Illuminate\Database\Eloquent\Relations\MorphOne;
...
public function image(): MorphOne
{
    # args1: Related Model -> Image
    # args2: Name of the relationship/method of the first arg.
    return $this->morphOne(Image::class, 'imageable');
}
...
```

On `app/Models/Post`
```
use Illuminate\Database\Eloquent\Relations\MorphOne;
...
public function image(): MorphOne
{
    # args1: Related Model -> Image
    # args2: Name of the relationship/method of the first arg.
    return $this->morphOne(Image::class, 'imageable');
}
...
```

On Tinker
```
$user = User::find(5);

$image = $user->image()->create([
    'url' => 'google.com'
])

$user->image;

# With POST
$post = Post::find(5);

$image = $post->image()->create([
    'url' => 'frompost.com'
]);

# WITH IMAGE
# has 2 records, 1 from User, and another 1 from Post.
# Amazing.
Image::all();


# POST WITH IMAGES

/*
    The "whereHas()" is used to query for related models that
    meet a certain condition. Example, Check if the post model has a 
    related image Model where the URl field contains the string of example.
*/
$postWithImages = Post::whereHas('image', function($query){
    $query->where('url', 'like', 'frompost.com');
})->get();
```


## ONE TO MANY POLYMORPHIC

This is used to define a relationship where a single models OWNS any amount of related models. 

**One To Man Polymorphic relationship are used when you need to relate a single model to multiple models in a single association.**

Real life example: Blog can have multiple Comments, but those Comments can come from different types of users, such as registered or anonymous users. With One to Many polymorphic relationship, you can easily associate thoce comments with the post without having to create separate table for each type of user. 

In this example, We'll using poly between 3 models: the Post, Video, and the Comment models.

On CLI
```
> php artisan make:model Video -m
> php artisan make:model Comment -m
```

`app/Models/Video.php`
```
use Illuminate\Database\Eloquent\Relations\MorphMany;
...
public function comments(): MorphMany
{
    # arg1: Related Model, Comment
    # arg2: Name of the Morph field, exampel: commentable
    return $this->morphMany(Comment::class,'commentable');
}
...
```

`app/Models/Post`
```
use Illuminate\Database\Eloquent\Relations\MorphMany;
...
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
...
```

`app/Models/Comment`
```
use Illuminate\Database\Eloquent\Relations\MorphTo;
...
public function commentable(): MorphTo
{
    return $this->morphTo();
}
...
```

ON tinker
```
$post = Post::find(10);

$comment = $post->comments()->create([
    'body' => 'This is a new comment'
]);


# retrieves posts
$post->comments;

$post->comments()->simplePaginate();


# video
Video::create([
    'title' => 'John Wick',
    'url' => 'google.com',
    'description' => 'Keanu Reeve is John Wick'
])

$video = Video::find(1);

$comment = $video->comments()->create([
    'body' => 'Buena pelicula'
]);

$video->comments;
$video->comments()->paginate();
```