# WordPress API SDK

A modern PHP SDK for interacting with the WordPress REST API. This SDK provides a clean, object-oriented interface for fetching posts, pages, menus, and other WordPress content with full pagination support.

## Project Plan & Architecture

### Features

- **Posts Resource**: Fetch posts with filtering, pagination, and sorting
- **Pages Resource**: Fetch pages with filtering, pagination, and sorting
- **Menus Resource**: Fetch menu data, menu items, and build hierarchical menu structures
- **Settings Resource**: Access WordPress site configuration and settings
- **Homepage Resource**: Intelligently detect and fetch WordPress homepage (static page or posts list)
- **Search**: Full-text search across posts, pages, and multiple content types
- **Polylang Support**: Filter posts and pages by language when using Polylang plugin
- **Pagination Support**: Built-in pagination with automatic link handling
- **Type Safety**: Full type hints and return types
- **Extensible**: Easy to add new resource types
- **Well Tested**: Comprehensive unit and integration tests

### Project Structure

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
│   │   ├── ClientTest.php
│   │   ├── Resources/
│   │   │   ├── PostsTest.php
│   │   │   ├── PagesTest.php
│   │   │   ├── MenusTest.php
│   │   │   └── SearchTest.php
│   │   └── Support/
│   │       └── QueryBuilderTest.php
│   └── Integration/               # Integration tests (Docker)
│       ├── PostsIntegrationTest.php
│       ├── PagesIntegrationTest.php
│       ├── MenusIntegrationTest.php
│       └── SearchIntegrationTest.php
├── docker/
│   └── wordpress/
│       └── init.sh                # WordPress initialization script
├── docker-compose.yml             # WordPress + MySQL setup
├── composer.json
├── phpunit.xml
└── README.md
```

### Architecture Overview

#### 1. Client Class
The main entry point for the SDK. Handles:
- HTTP client configuration (Guzzle)
- Base URL management
- Authentication (Application Passwords)
- Resource instantiation

```php
$client = new \WordPressApi\Client('https://example.com');
$posts = $client->posts()->all();
```

#### 2. Resource Classes
Each resource (Posts, Pages, Menus) extends `AbstractResource` and provides:
- `all()` - Fetch all items with pagination
- `get(int $id)` - Fetch single item by ID
- `query(array $params)` - Custom queries with filters

#### 3. Pagination
The `PaginatedResponse` class wraps responses and provides:
- Current page data
- Total count and pages
- `hasNextPage()`, `hasPreviousPage()`
- `nextPage()`, `previousPage()` methods
- Automatic header parsing (X-WP-Total, X-WP-TotalPages)

#### 4. Query Builder
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

WPML language filtering:
```php
// Fetch posts in German
$posts = $client->posts()
    ->language('de')
    ->get();

// Fetch posts in English
$posts = $client->posts()
    ->language('en')
    ->get();
```

#### 5. Search
The Search resource provides full-text search capabilities:
- Search across multiple content types (posts, pages)
- Filter by content type and subtype
- Full pagination support
- Relevance-based sorting
- WordPress REST API `/wp/v2/search` endpoint

```php
// Global search across all content
$results = $client->search()->search('wordpress')->get();

// Search only posts
$posts = $client->search()
    ->type('post')
    ->search('wordpress')
    ->get();

// Search with pagination
$results = $client->search()
    ->search('wordpress')
    ->perPage(20)
    ->page(2)
    ->get();

// Alternatively, search within specific resources
$posts = $client->posts()->search('wordpress')->get();
$pages = $client->pages()->search('wordpress')->get();
```

### Docker Setup for Testing

The integration tests run against a real WordPress instance managed by Docker:

**docker-compose.yml** provides:
- WordPress 6.x with Apache
- MySQL 8.x database
- phpMyAdmin (optional, for debugging)
- Exposed on port 8080

**Initialization**:
- Automatically creates test content (posts, pages, menus)
- Sets up authentication
- Configures permalinks

### Testing Strategy

#### Unit Tests
- Mock HTTP responses using Mockery
- Test business logic in isolation
- Fast execution, no external dependencies
- Focus on: query building, pagination logic, error handling

#### Integration Tests
- Run against Docker WordPress instance
- Test real API interactions
- Verify pagination, filtering, sorting
- Ensure compatibility with actual WordPress responses

### Implementation Plan

1. **Core Infrastructure** (Priority: High)
   - Client class with Guzzle HTTP client
   - AbstractResource base class
   - Exception hierarchy
   - PaginatedResponse wrapper

2. **Posts Resource** (Priority: High)
   - Fetch all posts with pagination
   - Get single post by ID
   - Filter by status, author, category, tag
   - Search functionality
   - Order by date, title, etc.

3. **Pages Resource** (Priority: High)
   - Fetch all pages with pagination
   - Get single page by ID
   - Hierarchical page support (parent/child)
   - Filter by status, author

4. **Menus Resource** (Priority: Medium) ✅
   - List all menus
   - Get menu by ID or slug
   - Fetch menu items with hierarchy
   - Build hierarchical menu structure from flat items
   - Get menu by location (primary, footer, etc.)
   - Convenience methods for fetching menus with items

5. **Settings Resource** (Priority: High) ✅
   - Access WordPress configuration settings
   - Get site title, description, URL
   - Get homepage settings (page_on_front, show_on_front)
   - Get format settings (date, time, timezone)
   - Get language and posts per page settings
   - Note: Most settings require authentication

6. **Homepage Resource** (Priority: High) ✅
   - Intelligently detect WordPress homepage type
   - Fetch static page if configured
   - Fall back to posts list if no static page
   - Integrate with Settings resource

7. **Search Resource** (Priority: High) ✅
   - Global search across content types
   - Filter by type (post, page, etc.)
   - Filter by subtype (post type)
   - Search method in Posts/Pages resources
   - Full pagination support

8. **Query Builder** (Priority: Medium) ✅
   - Fluent interface for building queries
   - Type-safe parameter validation
   - Support all WordPress REST API parameters
   - Polylang language filtering support

9. **Docker Setup** (Priority: High) ✅
   - Docker Compose configuration
   - WordPress initialization script
   - Test data seeding with Polylang

10. **Testing** (Priority: High) ✅
    - Unit tests for all classes
    - Integration tests for all resources
    - 104 total tests passing (63 unit + 41 integration)

### Usage Examples

#### Basic Usage
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

#### Advanced Query Builder
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

#### Fetching by Slug
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

#### Polylang Language Support
```php
// Fetch posts in a specific language (Polylang)
$germanPosts = $client->posts()
    ->language('de')
    ->where('status', 'publish')
    ->get();

// Fetch pages in English
$englishPages = $client->pages()
    ->language('en')
    ->get();

// Fetch pages in French
$frenchPages = $client->pages()
    ->language('fr')
    ->get();

// Combine language filter with search
$results = $client->posts()
    ->language('de')
    ->search('wordpress')
    ->where('status', 'publish')
    ->perPage(10)
    ->get();
```

#### Search Examples
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

#### Settings Examples
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

#### Homepage Examples
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

#### Menus Examples
```php
// Get all menus
$menus = $client->menus()->all();
echo "Total menus: " . $menus->getTotal() . "\n";

foreach ($menus->getData() as $menu) {
    echo "- [{$menu['id']}] {$menu['name']} (slug: {$menu['slug']})\n";
}

// Get menu by ID
$menu = $client->menus()->get(2);
echo $menu['name']; // "Primary Menu"

// Get menu by slug
$menu = $client->menus()->getBySlug('primary');
echo $menu['name'];

// Get menu by location (theme menu location)
$menu = $client->menus()->getByLocation('primary');
if ($menu) {
    echo "Primary menu: {$menu['name']}\n";
}

// Get menu items (flat array)
$items = $client->menus()->getItems($menuId);
foreach ($items as $item) {
    echo "{$item['title']} - {$item['url']}\n";
}

// Get menu with items (convenience method)
$result = $client->menus()->getWithItems($menuId);
$menu = $result['menu'];
$items = $result['items'];

// Get menu by slug with items
$result = $client->menus()->getBySlugWithItems('primary');

// Build hierarchical menu structure
$items = $client->menus()->getItems($menuId);
$hierarchy = $client->menus()->buildHierarchy($items);

// Display hierarchical menu
function displayMenu($items, $level = 0) {
    foreach ($items as $item) {
        $indent = str_repeat('  ', $level);
        echo "{$indent}- {$item['title']} ({$item['url']})\n";

        if (!empty($item['children'])) {
            displayMenu($item['children'], $level + 1);
        }
    }
}

displayMenu($hierarchy);

// Output:
// - Home (https://example.com)
// - About (https://example.com/about)
//   - Our Team (https://example.com/about/team)
//   - Contact (https://example.com/about/contact)
// - Blog (https://example.com/blog)
```

#### Authentication

WordPress Application Passwords (available since WordPress 5.6) are the recommended and secure way to authenticate with the WordPress REST API. They allow you to create unique passwords for applications without exposing your main WordPress password.

**How to create an Application Password in WordPress:**

1. Log in to your WordPress admin panel
2. Go to **Users > Profile** (or **Users > Your Profile**)
3. Scroll down to the **Application Passwords** section
4. Enter a name for your application (e.g., "My PHP SDK")
5. Click **Add New Application Password**
6. Copy the generated password immediately (it will only be shown once!)
7. The password format is: `xxxx xxxx xxxx xxxx xxxx xxxx` (24 characters with spaces)

**Using Application Passwords in the SDK:**

```php
// Application Passwords (WordPress 5.6+) - Recommended
$client = new Client('https://example.com', [
    'auth' => ['username', 'xxxx xxxx xxxx xxxx xxxx xxxx']
]);

// Real example (the password is shown only once when created):
$client = new Client('https://example.com', [
    'auth' => ['admin', 'abcd 1234 efgh 5678 ijkl 9012']
]);

// Spaces in the Application Password are optional
$client = new Client('https://example.com', [
    'auth' => ['admin', 'abcd1234efgh5678ijkl9012']
]);
```

**Benefits of Application Passwords:**
- More secure than using your main WordPress password
- Can be revoked individually without changing your main password
- Each application can have its own unique password
- Easy to manage and track which applications have access
- No need to share your actual WordPress password

### Polylang Integration

The SDK provides built-in support for Polylang (WordPress Multilingual Plugin) to fetch content in specific languages. Polylang is a free, open-source multilingual plugin for WordPress.

#### Prerequisites
- Polylang plugin must be installed and activated on the WordPress site
- Languages must be configured in WordPress (Settings > Languages)

#### Supported Language Parameters

**Language Filtering:**
- `lang` - Filter by language code (e.g., 'de', 'en', 'fr', 'es')

#### Language Codes
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

#### Polylang Usage Examples

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

#### Polylang Configuration Notes

1. **Default Language:** If no language is specified, Polylang returns content in the default language configured in WordPress
2. **Language Negotiation:** Polylang can auto-detect language from URL parameters or browser settings
3. **Separate Content:** Each language version has its own unique slug and URL
4. **REST API Support:** Polylang seamlessly integrates with the WordPress REST API using the `lang` parameter
5. **Free & Open Source:** Unlike WPML, Polylang is completely free and open source

### Installation & Setup

```bash
# Install dependencies
composer install

# Start WordPress Docker container
docker-compose up -d

# Wait for WordPress to initialize (30-60 seconds)
sleep 60

# Run tests
composer test

# Run only unit tests
composer test:unit

# Run only integration tests
composer test:integration
```

### Testing

This project includes both unit tests and integration tests.

#### Unit Tests
Unit tests run without any external dependencies and test business logic in isolation.

```bash
# Run all unit tests
composer test:unit

# Run with coverage (requires xdebug)
./vendor/bin/phpunit --testsuite unit --coverage-html coverage
```

**Stats:**
- 63 unit tests
- Tests: Client, QueryBuilder, Resources (Posts, Pages, Menus, Search)
- Fast execution (< 1 second)

#### Integration Tests
Integration tests run against a real WordPress instance (Docker) and test the full API integration.

```bash
# Start WordPress
docker-compose up -d

# Wait for WordPress to initialize (60 seconds)
sleep 60

# Run integration tests
composer test:integration

# Stop WordPress
docker-compose down
```

**Stats:**
- 41 integration tests
- Tests: Posts, Pages, Menus, Search, WPML/Polylang
- Tests real API interactions
- Auto-skipped if WordPress not running

**What Integration Tests Cover:**
- Fetching posts/pages by ID and slug
- Pagination and filtering
- Search functionality
- Language support (WPML)
- Error handling (404, etc.)
- Combined filters and queries

**Note:** Integration tests will automatically skip if WordPress is not running. No test failures will occur - they'll simply be marked as "Skipped".

#### Run All Tests
```bash
composer test
```

This runs both unit tests (63) and integration tests (41) for a total of 104 tests.

### Development Workflow

1. Start Docker WordPress: `docker-compose up -d`
2. Make changes to SDK code
3. Run unit tests: `composer test:unit`
4. Run integration tests: `composer test:integration`
5. Check static analysis: `composer analyse`
6. Stop Docker: `docker-compose down`

### API Reference

#### Client Methods
- `posts()` - Returns Posts resource
- `pages()` - Returns Pages resource
- `menus()` - Returns Menus resource
- `search()` - Returns Search resource
- `settings()` - Returns Settings resource
- `homepage()` - Returns Homepage resource

#### Resource Methods (Posts, Pages, Menus)
- `all(array $params = [])` - Fetch all items
- `get(int $id)` - Fetch single item by ID
- `getBySlug(string $slug)` - Fetch single item by slug
- `query(array $params)` - Custom query
- `search(string $term)` - Search within resource (Posts/Pages)
- `language(string $code)` - Filter by language (Polylang support, e.g., 'de', 'en', 'fr')

#### Menus Methods
- `all()` - Get all menus
- `get(int $id)` - Get menu by ID
- `getBySlug(string $slug)` - Get menu by slug
- `getByLocation(string $location)` - Get menu by theme location (e.g., 'primary', 'footer')
- `getItems(int $menuId)` - Get menu items for a specific menu
- `getWithItems(int $menuId)` - Get menu with its items
- `getBySlugWithItems(string $slug)` - Get menu by slug with its items
- `buildHierarchy(array $items)` - Build hierarchical menu structure from flat items

#### Settings Methods
- `all()` - Get all settings (requires authentication)
- `getTitle()` - Get site title
- `getDescription()` - Get site description
- `getUrl()` - Get site URL
- `getEmail()` - Get site email
- `getLanguage()` - Get site language
- `getTimezone()` - Get site timezone
- `getDateFormat()` - Get date format
- `getTimeFormat()` - Get time format
- `getShowOnFront()` - Get what shows on front page ('posts' or 'page')
- `getFrontPageId()` - Get front page ID (if static page)
- `getPostsPageId()` - Get posts page ID
- `hasStaticFrontPage()` - Check if a static page is set as front page
- `getPostsPerPage()` - Get posts per page setting
- `usesDefaultPermalinks()` - Check if site uses default (plain) permalink structure
- `getPermalinkStructure()` - Get permalink structure

#### Homepage Methods
- `get()` - Get homepage intelligently (returns static page or posts list)

#### Search Methods
- `search(string $term)` - Set search query
- `type(string $type)` - Filter by content type (post, page, etc.)
- `subtype(string $subtype)` - Filter by subtype (post type slug)
- `perPage(int $perPage)` - Set results per page
- `page(int $page)` - Set current page
- `get()` - Execute search and return results

#### Pagination Methods (PaginatedResponse)
- `getData()` - Get current page items
- `getTotal()` - Get total item count
- `getTotalPages()` - Get total page count
- `getCurrentPage()` - Get current page number
- `hasNextPage()` - Check if next page exists
- `hasPreviousPage()` - Check if previous page exists
- `nextPage()` - Fetch next page
- `previousPage()` - Fetch previous page

### Next Steps

After implementing the core SDK, consider adding:
- Media/Attachments resource
- Categories and Tags resources
- Users resource
- Custom post types support
- Caching layer (PSR-6/PSR-16)
- Rate limiting
- Retry logic with exponential backoff
- CLI tool for testing

### Requirements

- PHP 8.0 or higher
- Composer
- Docker and Docker Compose (for integration tests)
- WordPress 5.0+ (for the API endpoint)

### License

MIT
