# Object Controller

The `ObjectController` is the primary way to interact with your created objects.

## Options

When instantiating an instance of `ObjectController`, the object name and options are defined at the controller level,
rather than when performing actions. This allows for simple concern separation when dealing with individual controllers
for a specific domain or purpose.

For example, a controller to fetch the latest `Post` objects for a blog homepage would be setup like so:

```php
$postController = new \Conduit\Database\ObjectController("Post", [
  "limit" => 5
]);

$latestPosts = $postController->readAll();
```

While a controller to fetch the same objects, but in an admin panel or CMS could be setup like this:

```php
$postController = new \Conduit\Database\ObjectController("Post", [
  "includeUnpublished" => true,
  "customSort" => [
    "field" => "dateadded",
    "direction" => "desc"
  ]
]);

$adminPosts = $postController->readAll();
```

Calling the `readAll()` method on each of these controllers will then return different results depending on how the
controllers have been set up, despite returning the same objects.

### Defaults

```php
[
  "includeUnpublished" => false,
  "limit" => false,
  "customSort" => [
    "field" => "sortorder",
    "direction" => "desc"
  ]
]
```

| key                | type         | default                        | description                                                |
|--------------------|--------------|--------------------------------|------------------------------------------------------------|
| includeUnpublished | bool         | `false`                        | If true, all methods include objects marked as unpublished |
| limit              | int \| false | `false`                        | Limits the number of objects returned                      |
| customSort         | array        | [sortorder, desc](#customsort) | testing                                                    |

### customSort

The `customSort` option takes an array with both a `field` and `direction` key. The default values are as below;

```php
[
  "field"     => "sortorder",
  "direction" => "desc"
]
```