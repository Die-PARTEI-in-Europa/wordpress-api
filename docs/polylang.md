# Polylang Integration

The SDK provides built-in support for Polylang (WordPress Multilingual Plugin) to fetch content in specific languages. Polylang is a free, open-source multilingual plugin for WordPress.

## Prerequisites
- Polylang plugin must be installed and activated on the WordPress site
- Languages must be configured in WordPress (Settings > Languages)

## Supported Language Parameters

**Language Filtering:**
- `lang` - Filter by language code (e.g., 'de', 'en', 'fr', 'es')

## Language Codes
Common Polylang language codes:
- `de` - German (Deutsch)
- `en` - English
- `fr` - French (Français)
- `es` - Spanish (Español)
- `it` - Italian (Italiano)
- `nl` - Dutch (Nederlands)
- `pt` - Portuguese (Português)
- `pl` - Polish (Polski)
- `ru` - Russian (Русский)

## Usage Examples

**Fetch posts by language:**
```php
// German posts
$germanPosts = $client->posts()
    ->language('de')
    ->get();

// English posts
$englishPosts = $client->posts()
    ->language('en')
    ->get();

// French pages
$frenchPages = $client->pages()
    ->language('fr')
    ->where('status', 'publish')
    ->get();
```

**Complex language queries:**
```php
// Search German posts with filters
$results = $client->posts()
    ->language('de')
    ->search('WordPress')
    ->where('status', 'publish')
    ->where('category', 5)
    ->orderBy('date', 'desc')
    ->perPage(20)
    ->get();

// Get published English pages with hierarchy
$pages = $client->pages()
    ->language('en')
    ->where('status', 'publish')
    ->where('parent', 0) // top-level pages only
    ->orderBy('menu_order', 'asc')
    ->get();
```

**Multilingual search:**
```php
// Search across all content in German
$searchResults = $client->search()
    ->search('Tutorial')
    ->type('post')
    ->language('de')
    ->perPage(10)
    ->get();

// Search French pages
$frenchResults = $client->pages()
    ->language('fr')
    ->search('contact')
    ->get();
```

## Configuration Notes

1. **Default Language:** If no language is specified, Polylang returns content in the default language configured in WordPress
2. **Language Negotiation:** Polylang can auto-detect language from URL parameters or browser settings
3. **Separate Content:** Each language version has its own unique slug and URL
4. **REST API Support:** Polylang seamlessly integrates with the WordPress REST API using the `lang` parameter
5. **Free & Open Source:** Unlike WPML, Polylang is completely free and open source
