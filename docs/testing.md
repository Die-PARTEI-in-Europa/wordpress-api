# Testing & Development

## Development Setup

For contributing to the SDK itself (not needed if you just want to use it as a dependency):

```bash
# Clone the repository and install dependencies
git clone git@github.com:Die-PARTEI-in-Europa/wordpress-api.git
cd wordpress-api
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

## Docker Setup for Testing

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

## Testing Strategy

### Unit Tests
- Mock HTTP responses using Mockery
- Test business logic in isolation
- Fast execution, no external dependencies
- Focus on: query building, pagination logic, error handling

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

### Integration Tests
- Run against Docker WordPress instance
- Test real API interactions
- Verify pagination, filtering, sorting
- Ensure compatibility with actual WordPress responses

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
- Language support (WPML/Polylang)
- Error handling (404, etc.)
- Combined filters and queries

**Note:** Integration tests will automatically skip if WordPress is not running. No test failures will occur - they'll simply be marked as "Skipped".

### Run All Tests
```bash
composer test
```

This runs both unit tests (63) and integration tests (41) for a total of 104 tests.

## Development Workflow

1. Start Docker WordPress: `docker-compose up -d`
2. Make changes to SDK code
3. Run unit tests: `composer test:unit`
4. Run integration tests: `composer test:integration`
5. Check static analysis: `composer analyse`
6. Stop Docker: `docker-compose down`
