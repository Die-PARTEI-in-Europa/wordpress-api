# Homepage

```php
// Get homepage intelligently
// Automatically detects if homepage is a static page or posts list

$homepage = $client->homepage()->get();

if ($homepage['type'] === 'page') {
    // Static page is set as homepage
    $page = $homepage['page'];
    echo "Homepage: {$page['title']['rendered']}\n";
    echo $page['content']['rendered'];
} else {
    // Homepage shows latest posts
    echo $homepage['message']; // "Homepage shows latest blog posts"
    echo "Total posts: {$homepage['total_posts']}\n";

    foreach ($homepage['posts'] as $post) {
        echo "- {$post['title']['rendered']}\n";
    }
}

// The homepage resource automatically:
// 1. Checks Settings for page_on_front
// 2. Returns static page if configured
// 3. Falls back to latest posts if no static page
// 4. Respects posts_per_page setting
```

See [Settings](settings.md) and [API Reference](api-reference.md#homepage-methods).
