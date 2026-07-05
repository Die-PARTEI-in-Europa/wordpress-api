# API Reference (Cheat Sheet)

This is a quick method-name reference. For full type signatures, parameter docs and inline examples generated from source, see the [API Reference site](https://die-partei-in-europa.github.io/wordpress-api/reference/) (phpDocumentor).

## Client Methods
- `posts()` - Returns Posts resource
- `pages()` - Returns Pages resource
- `menus()` - Returns Menus resource
- `search()` - Returns Search resource
- `settings()` - Returns Settings resource
- `homepage()` - Returns Homepage resource

## Resource Methods (Posts, Pages, Menus)
- `all(array $params = [])` - Fetch all items
- `get(int $id)` - Fetch single item by ID
- `getBySlug(string $slug)` - Fetch single item by slug
- `query(array $params)` - Custom query
- `search(string $term)` - Search within resource (Posts/Pages)
- `language(string $code)` - Filter by language (Polylang support, e.g., 'de', 'en', 'fr')

## Menus Methods
- `all()` - Get all menus
- `get(int $id)` - Get menu by ID
- `getBySlug(string $slug)` - Get menu by slug
- `getByLocation(string $location)` - Get menu by theme location (e.g., 'primary', 'footer')
- `getItems(int $menuId)` - Get menu items for a specific menu
- `getWithItems(int $menuId)` - Get menu with its items
- `getBySlugWithItems(string $slug)` - Get menu by slug with its items
- `buildHierarchy(array $items)` - Build hierarchical menu structure from flat items

## Settings Methods
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

## Homepage Methods
- `get()` - Get homepage intelligently (returns static page or posts list)

## Search Methods
- `search(string $term)` - Set search query
- `type(string $type)` - Filter by content type (post, page, etc.)
- `subtype(string $subtype)` - Filter by subtype (post type slug)
- `perPage(int $perPage)` - Set results per page
- `page(int $page)` - Set current page
- `get()` - Execute search and return results

## Pagination Methods (PaginatedResponse)
- `getData()` - Get current page items
- `getTotal()` - Get total item count
- `getTotalPages()` - Get total page count
- `getCurrentPage()` - Get current page number
- `hasNextPage()` - Check if next page exists
- `hasPreviousPage()` - Check if previous page exists
- `nextPage()` - Fetch next page
- `previousPage()` - Fetch previous page
