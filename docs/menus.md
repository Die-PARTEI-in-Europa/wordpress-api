# Menus

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

See [API Reference](api-reference.md#menus-methods) for the full `Menus` method list.
