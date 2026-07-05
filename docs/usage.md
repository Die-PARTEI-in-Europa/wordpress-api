# Usage

## Basic Usage

```php
use WordPressApi\Client;

// Initialize client
$client = new Client('https://example.com');

// Fetch posts
$posts = $client->posts()->all();
foreach ($posts->getData() as $post) {
    echo $post['title']['rendered'] . "\n";
}

// Pagination
if ($posts->hasNextPage()) {
    $nextPage = $posts->nextPage();
}

// Get single post by ID
$post = $client->posts()->get(123);

// Get single post by slug
$post = $client->posts()->getBySlug('hello-world');

// Query with filters
$filtered = $client->posts()->query([
    'status' => 'publish',
    'per_page' => 10,
    'orderby' => 'date',
    'order' => 'desc'
]);
```

## Advanced Query Builder

```php
$posts = $client->posts()
    ->where('status', 'publish')
    ->where('author', 1)
    ->search('wordpress')
    ->orderBy('date', 'desc')
    ->perPage(20)
    ->page(2)
    ->get();
```

## Fetching by Slug

```php
// Get a post by slug
$post = $client->posts()->getBySlug('my-awesome-post');
echo $post['title']['rendered'];

// Get a page by slug
$page = $client->pages()->getBySlug('about-us');
echo $page['content']['rendered'];

// Get post by slug with error handling
try {
    $post = $client->posts()->getBySlug('non-existent-slug');
} catch (\WordPressApi\Exceptions\NotFoundException $e) {
    echo "Post not found: " . $e->getMessage();
}

// Combine with Polylang - get German version of a post by slug
// Note: Use the German slug, as Polylang uses different slugs per language
$germanPost = $client->posts()
    ->language('de')
    ->where('slug', 'mein-toller-beitrag')
    ->get();
```

See also: [Search](search.md), [Settings](settings.md), [Homepage](homepage.md), [Menus](menus.md), [Polylang Integration](polylang.md), [Authentication](authentication.md).
