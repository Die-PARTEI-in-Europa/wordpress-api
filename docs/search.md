# Search

```php
// Global search across all content types
$results = $client->search()->search('wordpress development')->get();

// Iterate through search results
foreach ($results->getData() as $item) {
    echo $item['title'] . " ({$item['type']})\n";
    echo $item['url'] . "\n\n";
}

// Search only posts
$postResults = $client->search()
    ->type('post')
    ->search('wordpress')
    ->get();

// Search with pagination
$page1 = $client->search()
    ->search('api')
    ->perPage(10)
    ->page(1)
    ->get();

if ($page1->hasNextPage()) {
    $page2 = $page1->nextPage();
}

// Search within specific resources
$posts = $client->posts()
    ->search('wordpress')
    ->where('status', 'publish')
    ->orderBy('relevance', 'desc')
    ->get();

$pages = $client->pages()
    ->search('contact')
    ->get();

// Complex search with multiple filters
$results = $client->search()
    ->search('wordpress plugin')
    ->type('post')
    ->subtype('post') // or 'custom-post-type'
    ->perPage(20)
    ->get();
```

See [API Reference](api-reference.md#search-methods) for the full `Search` method list.
