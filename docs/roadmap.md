# Roadmap

## Implementation Status

1. **Core Infrastructure** ✅ (Priority: High)
   - Client class with Guzzle HTTP client
   - AbstractResource base class
   - Exception hierarchy
   - PaginatedResponse wrapper

2. **Posts Resource** ✅ (Priority: High)
   - Fetch all posts with pagination
   - Get single post by ID
   - Filter by status, author, category, tag
   - Search functionality
   - Order by date, title, etc.

3. **Pages Resource** ✅ (Priority: High)
   - Fetch all pages with pagination
   - Get single page by ID
   - Hierarchical page support (parent/child)
   - Filter by status, author

4. **Menus Resource** ✅ (Priority: Medium)
   - List all menus
   - Get menu by ID or slug
   - Fetch menu items with hierarchy
   - Build hierarchical menu structure from flat items
   - Get menu by location (primary, footer, etc.)
   - Convenience methods for fetching menus with items

5. **Settings Resource** ✅ (Priority: High)
   - Access WordPress configuration settings
   - Get site title, description, URL
   - Get homepage settings (page_on_front, show_on_front)
   - Get format settings (date, time, timezone)
   - Get language and posts per page settings
   - Note: Most settings require authentication

6. **Homepage Resource** ✅ (Priority: High)
   - Intelligently detect WordPress homepage type
   - Fetch static page if configured
   - Fall back to posts list if no static page
   - Integrate with Settings resource

7. **Search Resource** ✅ (Priority: High)
   - Global search across content types
   - Filter by type (post, page, etc.)
   - Filter by subtype (post type)
   - Search method in Posts/Pages resources
   - Full pagination support

8. **Query Builder** ✅ (Priority: Medium)
   - Fluent interface for building queries
   - Type-safe parameter validation
   - Support all WordPress REST API parameters
   - Polylang language filtering support

9. **Docker Setup** ✅ (Priority: High)
   - Docker Compose configuration
   - WordPress initialization script
   - Test data seeding with Polylang

10. **Testing** ✅ (Priority: High)
    - Unit tests for all classes
    - Integration tests for all resources
    - 104 total tests passing (63 unit + 41 integration)

11. **Documentation** ✅ (Priority: Medium)
    - Guides split into `docs/`
    - Auto-generated API reference (phpDocumentor) published to GitHub Pages

## Next Steps

Ideas for future additions:
- Media/Attachments resource
- Categories and Tags resources
- Users resource
- Custom post types support
- Caching layer (PSR-6/PSR-16)
- Rate limiting
- Retry logic with exponential backoff
- CLI tool for testing

## Requirements

- PHP 8.0 or higher
- Composer
- Docker and Docker Compose (for integration tests)
- WordPress 5.0+ (for the API endpoint)
