# Integration Tests

Integration tests for the WordPress API SDK that run against a real WordPress instance.

## Overview

These tests verify that the SDK correctly interacts with a real WordPress REST API. They test:

- **Posts** - Fetching, filtering, searching, pagination
- **Pages** - Fetching, filtering, searching
- **Menus** - Fetching menus, menu items, hierarchy building
- **Search** - Global search, type filtering, language support
- **WPML/Polylang** - Language filtering (if WPML/Polylang is installed)

## Running Integration Tests

### Quick Start

```bash
# Use the helper script
./run-integration-tests.sh
```

### Manual Steps

```bash
# 1. Start WordPress
docker-compose up -d

# 2. Wait for initialization
sleep 60

# 3. Run tests
composer test:integration

# 4. Stop WordPress (optional)
docker-compose down
```

## Test Structure

### IntegrationTestCase
Base class for all integration tests. Provides:
- WordPress availability check
- Automatic test skipping if WordPress is not running
- Shared client initialization

### Test Files
- `PostsIntegrationTest.php` - 14 tests for Posts resource
- `PagesIntegrationTest.php` - 10 tests for Pages resource
- `MenusIntegrationTest.php` - 8 tests for Menus resource
- `SearchIntegrationTest.php` - 9 tests for Search resource

## Test Coverage

### Posts Tests
- ✅ Fetch all posts
- ✅ Fetch post by ID
- ✅ Fetch post by slug
- ✅ Exception handling (invalid ID/slug)
- ✅ Query with filters
- ✅ Search functionality
- ✅ Pagination (next/previous)
- ✅ Ordering (asc/desc)
- ✅ Language filtering (WPML)
- ✅ Combined filters

### Pages Tests
- ✅ Fetch all pages
- ✅ Fetch page by ID
- ✅ Fetch page by slug
- ✅ Exception handling
- ✅ Query with filters
- ✅ Search functionality
- ✅ Language filtering
- ✅ Pagination

### Menus Tests
- ✅ Fetch all menus
- ✅ Fetch menu by ID
- ✅ Fetch menu by slug
- ✅ Exception handling (invalid slug)
- ✅ Get menu by location (unassigned location returns null)
- ✅ Fetch menu items
- ✅ Get menu with items (convenience method)
- ✅ Build hierarchy from real menu items

### Search Tests
- ✅ Global search
- ✅ Search by type (post/page)
- ✅ Search pagination
- ✅ Language filtering
- ✅ Empty search query
- ✅ No results handling
- ✅ Combined filters

## WordPress Setup

The Docker setup (`docker-compose.yml`) provides:
- WordPress 6.x
- MySQL 8.x
- Pre-seeded test data:
  - 20 test posts
  - 10 test pages
  - Categories and tags
- Accessible at `http://localhost:8080`

## Automatic Test Skipping

Tests automatically skip if:
- WordPress is not running
- WordPress is not accessible
- Network issues prevent connection

This ensures tests don't fail in CI/CD environments where WordPress might not be available.

## WordPress Credentials

Default credentials (configured in `docker-compose.yml`):
- URL: `http://localhost:8080`
- Admin User: `admin`
- Admin Password: `admin`

## Adding New Tests

1. Extend `IntegrationTestCase`
2. Use `$this->client` to access the SDK
3. Tests will auto-skip if WordPress is not running
4. Add meaningful assertions

Example:
```php
<?php

namespace WordPressApi\Tests\Integration;

class CustomIntegrationTest extends IntegrationTestCase
{
    public function testSomething(): void
    {
        $result = $this->client->posts()->all();
        $this->assertNotEmpty($result->getData());
    }
}
```

## Troubleshooting

### WordPress not starting
```bash
# Check Docker logs
docker-compose logs wordpress

# Restart containers
docker-compose down
docker-compose up -d
```

### Tests timing out
Increase sleep time before running tests:
```bash
sleep 90  # Wait longer for WordPress
composer test:integration
```

### Port 8080 already in use
Edit `docker-compose.yml` and change the port:
```yaml
ports:
  - "8081:80"  # Use 8081 instead
```

Then update the base URL in tests or use environment variable:
```bash
export WORDPRESS_URL=http://localhost:8081
```

## CI/CD Integration

Integration tests work great in CI/CD pipelines:

```yaml
# Example GitHub Actions
- name: Start WordPress
  run: docker-compose up -d

- name: Wait for WordPress
  run: sleep 60

- name: Run Integration Tests
  run: composer test:integration

- name: Stop WordPress
  run: docker-compose down
```

If WordPress is not available, tests will skip gracefully without failing the build.
