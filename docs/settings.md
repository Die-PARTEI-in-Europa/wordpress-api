# Settings

```php
// Get WordPress site configuration
// Note: Most settings require authentication

// Get site title and description
$title = $client->settings()->getTitle();
$description = $client->settings()->getDescription();
$url = $client->settings()->getUrl();

echo "Site: $title\n";
echo "Description: $description\n";
echo "URL: $url\n";

// Get homepage settings
$showOnFront = $client->settings()->getShowOnFront(); // 'posts' or 'page'
$frontPageId = $client->settings()->getFrontPageId(); // Page ID if static page
$postsPageId = $client->settings()->getPostsPageId(); // Blog page ID

if ($showOnFront === 'page' && $frontPageId > 0) {
    $homepage = $client->pages()->get($frontPageId);
    echo "Homepage: {$homepage['title']['rendered']}\n";
}

// Get format and language settings
$timezone = $client->settings()->getTimezone();
$dateFormat = $client->settings()->getDateFormat();
$timeFormat = $client->settings()->getTimeFormat();
$language = $client->settings()->getLanguage();
$postsPerPage = $client->settings()->getPostsPerPage();

// Get all settings at once (requires authentication)
$allSettings = $client->settings()->all();
print_r($allSettings);

// Using with Application Password authentication
$authClient = new Client('https://example.com', [
    'auth' => ['username', 'xxxx xxxx xxxx xxxx xxxx xxxx']
]);
$settings = $authClient->settings()->all();
```

See [Authentication](authentication.md) for how to create Application Passwords, and [API Reference](api-reference.md#settings-methods) for the full method list.
