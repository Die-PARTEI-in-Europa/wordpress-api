<?php

require_once __DIR__ . '/vendor/autoload.php';

use WordPressApi\Client;
use WordPressApi\Exceptions\WordPressApiException;

// Initialize the client
$client = new Client('http://localhost:8080');

// Or with Application Password authentication (recommended)
// Generate Application Password in WordPress: Users > Profile > Application Passwords
// $client = new Client('http://localhost:8080', [
//     'auth' => ['admin', 'xxxx xxxx xxxx xxxx xxxx xxxx']
// ]);

try {
    echo "=== Fetching all posts ===\n";
    $posts = $client->posts()->all();
    echo "Total posts: " . $posts->getTotal() . "\n";
    echo "Total pages: " . $posts->getTotalPages() . "\n\n";

    foreach ($posts->getData() as $post) {
        echo "- {$post['title']['rendered']}\n";
    }

    echo "\n=== Fetching posts with filters ===\n";
    $filteredPosts = $client->posts()
        ->where('status', 'publish')
        ->orderBy('date', 'desc')
        ->perPage(5)
        ->get();

    foreach ($filteredPosts->getData() as $post) {
        echo "- {$post['title']['rendered']}\n";
    }

    echo "\n=== Searching posts ===\n";
    $searchResults = $client->posts()
        ->search('test')
        ->perPage(3)
        ->get();

    echo "Found: " . $searchResults->getTotal() . " posts\n";
    foreach ($searchResults->getData() as $post) {
        echo "- {$post['title']['rendered']}\n";
    }

    echo "\n=== Polylang Language Support ===\n";
    echo "Fetching German posts:\n";
    $germanPosts = $client->posts()
        ->language('de')
        ->where('status', 'publish')
        ->perPage(5)
        ->get();

    echo "Found: " . $germanPosts->getTotal() . " German posts\n";

    echo "\nFetching English posts:\n";
    $englishPosts = $client->posts()
        ->language('en')
        ->where('status', 'publish')
        ->perPage(5)
        ->get();

    echo "Found: " . $englishPosts->getTotal() . " English posts\n";

    echo "\nFetching French posts:\n";
    $frenchPosts = $client->posts()
        ->language('fr')
        ->where('status', 'publish')
        ->perPage(5)
        ->get();

    echo "Found: " . $frenchPosts->getTotal() . " French posts\n";

    echo "\n=== Combined filters with language ===\n";
    $combinedResults = $client->posts()
        ->language('de')
        ->search('wordpress')
        ->where('status', 'publish')
        ->orderBy('date', 'desc')
        ->perPage(10)
        ->get();

    echo "Found: " . $combinedResults->getTotal() . " German posts matching 'wordpress'\n";

    echo "\n=== Fetching a single post by ID ===\n";
    try {
        $singlePost = $client->posts()->get(1);
        echo "Post: {$singlePost['title']['rendered']}\n";
    } catch (WordPressApiException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n=== Fetching posts by slug ===\n";
    try {
        // Try to fetch the first test post by slug
        // Note: WordPress generates slugs from titles, e.g., "Test Post 1" becomes "test-post-1"
        $postBySlug = $client->posts()->getBySlug('test-post-1');
        echo "Found post by slug: {$postBySlug['title']['rendered']}\n";
        echo "Post ID: {$postBySlug['id']}\n";
    } catch (WordPressApiException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n=== Fetching page by slug ===\n";
    try {
        $pageBySlug = $client->pages()->getBySlug('test-page-1');
        echo "Found page by slug: {$pageBySlug['title']['rendered']}\n";
    } catch (WordPressApiException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }

    echo "\n=== Pagination example ===\n";
    $page1 = $client->posts()->perPage(5)->page(1)->get();
    echo "Page 1 - Posts: " . count($page1->getData()) . "\n";

    if ($page1->hasNextPage()) {
        echo "Has next page: Yes\n";
        $page2 = $page1->nextPage();
        echo "Page 2 - Posts: " . count($page2->getData()) . "\n";
    }

    echo "\n=== Search across all content ===\n";
    $globalSearch = $client->search()->search('test')->get();
    echo "Global search results: " . $globalSearch->getTotal() . "\n";

    echo "\n=== Search only posts ===\n";
    $postSearch = $client->search()
        ->search('test')
        ->type('post')
        ->get();
    echo "Post search results: " . $postSearch->getTotal() . "\n";

    echo "\n=== Fetching pages ===\n";
    $pages = $client->pages()->all();
    echo "Total pages: " . $pages->getTotal() . "\n";

    foreach ($pages->getData() as $page) {
        echo "- {$page['title']['rendered']}\n";
    }

} catch (WordPressApiException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
