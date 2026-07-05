# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0] - 2026-07-05

### Added
- `ConnectionException` (extends `WordPressApiException`) — thrown when the backend is unreachable at the transport level (connection refused, DNS failure, timeout, e.g. the VPN route is down), so callers can distinguish "backend down" from HTTP-level errors and serve a cached/degraded response.
- `connect_timeout` (default 5s, configurable) on the HTTP client — fail fast when the route is down instead of blocking up to the full request timeout.
- HTTP-mocked regression tests (`ErrorHandlingTest`) for transport errors, pagination boundaries and settings memoization — the previous suite never exercised the network path.

### Changed
- **BREAKING:** `PaginatedResponse::nextPage()` / `previousPage()` now return `null` at the last/first page instead of `$this` (which was an infinite-loop trap). Return type is now `?self`.
- Transport error handling now also catches connection failures (→ `ConnectionException`) and 5xx/transfer errors (→ `WordPressApiException`) across `AbstractResource`, `QueryBuilder::get()`, `PaginatedResponse` and `Menus`. Previously only 4xx `ClientException` was handled and everything else propagated as raw Guzzle exceptions.
- `Settings` getters memoize the settings payload per instance — previously each getter fired its own HTTP request.
- `Homepage::get()` no longer silently falls back to the posts list on connection/server errors; it only falls back when settings genuinely require authentication.

### Fixed
- Removed a stray shell fragment in the `Settings` class docblock.

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

[0.2.0]: https://github.com/Die-PARTEI-in-Europa/wordpress-api/releases/tag/v0.2.0
[0.1.0]: https://github.com/Die-PARTEI-in-Europa/wordpress-api/releases/tag/v0.1.0
