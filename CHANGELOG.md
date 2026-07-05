# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.1.0] - 2026-07-05

### Added
- `Client` with Guzzle-based HTTP transport and Application Password authentication.
- `Posts`, `Pages`, `Menus`, `Search`, `Settings` and `Homepage` resources.
- Fluent `QueryBuilder` (`where`, `orderBy`, `perPage`, `page`, `search`, `language`).
- `PaginatedResponse` with `hasNextPage()`/`nextPage()` and WP header-based totals.
- Polylang language filtering across Posts, Pages and Search.
- Menu hierarchy building (`buildHierarchy()`) from flat menu-item lists.
- Exception hierarchy (`WordPressApiException`, `NotFoundException`, `AuthenticationException`).
- Docker Compose setup (WordPress + MySQL + phpMyAdmin) for integration testing.
- 104 tests (63 unit, 41 integration).
- Guides split into `docs/`, plus an auto-generated phpDocumentor API reference published to GitHub Pages.
- MIT license.

[0.1.0]: https://github.com/Die-PARTEI-in-Europa/wordpress-api/releases/tag/v0.1.0
