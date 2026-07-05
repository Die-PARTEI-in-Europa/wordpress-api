# WordPress API SDK

[![Packagist](https://img.shields.io/packagist/v/parteieuropa/wordpress-api.svg)](https://packagist.org/packages/parteieuropa/wordpress-api)
[![License](https://img.shields.io/packagist/l/parteieuropa/wordpress-api.svg)](https://packagist.org/packages/parteieuropa/wordpress-api)

A modern PHP SDK for interacting with the WordPress REST API. This SDK provides a clean, object-oriented interface for fetching posts, pages, menus, and other WordPress content with full pagination support.

**📘 [Full documentation & API reference](https://die-partei-in-europa.github.io/wordpress-api/)** — auto-generated from source, always up to date.

## Installation

Install via [Composer](https://getcomposer.org) from [Packagist](https://packagist.org/packages/parteieuropa/wordpress-api):

```bash
composer require parteieuropa/wordpress-api
```

Requires PHP 8.0 or higher.

## Quick Start

```php
use WordPressApi\Client;

$client = new Client('https://example.com');

$posts = $client->posts()->all();
foreach ($posts->getData() as $post) {
    echo $post['title']['rendered'] . "\n";
}
```

## Features

- **Posts & Pages**: Fetch, filter, sort and paginate with a fluent query builder
- **Menus**: List menus, fetch items, build hierarchical menu trees
- **Settings & Homepage**: Read site configuration, intelligently resolve the homepage
- **Search**: Full-text search across posts, pages, and multiple content types
- **Polylang Support**: Filter posts and pages by language
- **Pagination**: Built-in pagination with automatic link handling
- **Type Safety**: Full PHP 8 type hints and return types
- **Well Tested**: 104 unit & integration tests

## Documentation

Detailed guides live in [`docs/`](docs/):

- [Architecture](docs/architecture.md)
- [Usage](docs/usage.md)
- [Authentication](docs/authentication.md)
- [Settings](docs/settings.md)
- [Homepage](docs/homepage.md)
- [Menus](docs/menus.md)
- [Search](docs/search.md)
- [Polylang Integration](docs/polylang.md)
- [Testing & Development](docs/testing.md)
- [API Reference (cheat sheet)](docs/api-reference.md)
- [Roadmap](docs/roadmap.md)

The full class/method reference (generated from PHPDoc via phpDocumentor) is published at [die-partei-in-europa.github.io/wordpress-api](https://die-partei-in-europa.github.io/wordpress-api/).

## Testing

```bash
composer test           # unit + integration
composer test:unit      # unit only
composer test:integration  # requires docker-compose up -d
```

See [docs/testing.md](docs/testing.md) for the full setup.

## License

MIT — see [LICENSE](LICENSE).
