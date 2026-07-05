<?php

declare(strict_types=1);

namespace WordPressApi\Resources;

use GuzzleHttp\Exception\ClientException;
use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Exceptions\WordPressApiException;

class Menus extends AbstractResource
{
    /**
     * Get menu items for a specific menu
     *
     * @param int $menuId Menu ID or term_id
     * @return array<int, array<string, mixed>>
     * @throws WordPressApiException
     */
    public function getItems(int $menuId): array
    {
        try {
            $menuItemsEndpoint = rtrim($this->baseUrl, '/') . '/wp/v2/menu-items';
            $response = $this->client->get($menuItemsEndpoint, [
                'query' => ['menus' => $menuId]
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return is_array($data) ? $data : [];
        } catch (ClientException $e) {
            throw new WordPressApiException("Failed to get menu items: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get menu by slug
     *
     * @throws NotFoundException
     */
    public function getBySlug(string $slug): array
    {
        try {
            $response = $this->client->get($this->endpoint, ['query' => ['slug' => $slug]]);
            $data = json_decode((string) $response->getBody(), true);

            if (empty($data) || !is_array($data)) {
                throw new NotFoundException("Menu with slug '{$slug}' not found", 404);
            }

            return $data[0];
        } catch (ClientException $e) {
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get menu by location (e.g., 'primary', 'footer')
     *
     * Note: This requires the menu location to be set in WordPress theme.
     *
     * @param string $location Menu location slug
     * @return array<string, mixed>|null
     * @throws WordPressApiException
     */
    public function getByLocation(string $location): ?array
    {
        try {
            // Get all menus and find the one assigned to this location
            $menus = $this->all();

            foreach ($menus->getData() as $menu) {
                if (isset($menu['locations']) && in_array($location, $menu['locations'])) {
                    return $menu;
                }
            }

            return null;
        } catch (\Exception $e) {
            throw new WordPressApiException("Failed to get menu by location: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get menu with its items
     *
     * This is a convenience method that fetches both the menu and its items.
     *
     * @param int $menuId Menu ID
     * @return array{menu: array, items: array}
     * @throws NotFoundException
     * @throws WordPressApiException
     */
    public function getWithItems(int $menuId): array
    {
        $menu = $this->get($menuId);
        $items = $this->getItems($menuId);

        return [
            'menu' => $menu,
            'items' => $items,
        ];
    }

    /**
     * Get menu by slug with its items
     *
     * @param string $slug Menu slug
     * @return array{menu: array, items: array}
     * @throws NotFoundException
     * @throws WordPressApiException
     */
    public function getBySlugWithItems(string $slug): array
    {
        $menu = $this->getBySlug($slug);
        $items = $this->getItems($menu['id']);

        return [
            'menu' => $menu,
            'items' => $items,
        ];
    }

    /**
     * Build hierarchical menu structure
     *
     * Organizes flat menu items into a hierarchical tree structure.
     *
     * @param array<int, array<string, mixed>> $items Flat array of menu items
     * @return array<int, array<string, mixed>> Hierarchical menu structure
     */
    public function buildHierarchy(array $items): array
    {
        $tree = [];
        $indexed = [];

        // First pass: index all items by ID
        foreach ($items as $item) {
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }

        // Second pass: build tree structure
        foreach ($indexed as $id => &$item) {
            if ($item['parent'] === 0) {
                // Top-level item - will be added later
            } else {
                // Child item - add to parent
                if (isset($indexed[$item['parent']])) {
                    $indexed[$item['parent']]['children'][] = &$item;
                }
            }
        }
        unset($item); // Break reference

        // Third pass: add top-level items to tree
        foreach ($indexed as $item) {
            if ($item['parent'] === 0) {
                $tree[] = $item;
            }
        }

        return $tree;
    }
}
