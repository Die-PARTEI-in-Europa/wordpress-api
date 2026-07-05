# Architecture

## Project Structure

```
wordpress-api/
├── src/
│   ├── Client.php                 # Main SDK client
│   ├── Resources/
│   │   ├── AbstractResource.php   # Base resource class
│   │   ├── Posts.php              # Posts resource
│   │   ├── Pages.php              # Pages resource
│   │   ├── Menus.php              # Menus resource
│   │   ├── Settings.php           # Settings resource
│   │   ├── Homepage.php           # Homepage resource
│   │   └── Search.php             # Search resource
│   ├── Contracts/
│   │   └── ResourceInterface.php  # Resource interface
│   ├── Exceptions/
│   │   ├── WordPressApiException.php
│   │   ├── NotFoundException.php
│   │   └── AuthenticationException.php
│   └── Support/
│       ├── PaginatedResponse.php  # Pagination wrapper
│       └── QueryBuilder.php       # Query string builder
├── tests/
│   ├── Unit/                      # Unit tests (mocked)
│   └── Integration/                # Integration tests (Docker)
├── docker/
│   └── wordpress/
│       └── init.sh                # WordPress initialization script
├── docker-compose.yml             # WordPress + MySQL setup
├── composer.json
├── phpunit.xml
└── README.md
```

## Overview

### 1. Client Class
The main entry point for the SDK. Handles:
- HTTP client configuration (Guzzle)
- Base URL management
- Authentication (Application Passwords)
- Resource instantiation

```php
$client = new \WordPressApi\Client('https://example.com');
$posts = $client->posts()->all();
```

### 2. Resource Classes
Each resource (Posts, Pages, Menus) extends `AbstractResource` and provides:
- `all()` - Fetch all items with pagination
- `get(int $id)` - Fetch single item by ID
- `query(array $params)` - Custom queries with filters

### 3. Pagination
The `PaginatedResponse` class wraps responses and provides:
- Current page data
- Total count and pages
- `hasNextPage()`, `hasPreviousPage()`
- `nextPage()`, `previousPage()` methods
- Automatic header parsing (X-WP-Total, X-WP-TotalPages)

### 4. Query Builder
Fluent interface for building WordPress API queries:
```php
$posts = $client->posts()
    ->where('status', 'publish')
    ->where('author', 1)
    ->orderBy('date', 'desc')
    ->perPage(20)
    ->page(2)
    ->get();
```

Language filtering (Polylang, see [Polylang Integration](polylang.md)):
```php
$posts = $client->posts()->language('de')->get();
```

### 5. Search
The Search resource provides full-text search capabilities:
- Search across multiple content types (posts, pages)
- Filter by content type and subtype
- Full pagination support
- Relevance-based sorting
- WordPress REST API `/wp/v2/search` endpoint

See [Search](search.md) for detailed examples.
