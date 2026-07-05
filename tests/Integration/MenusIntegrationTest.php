<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Support\PaginatedResponse;

class MenusIntegrationTest extends IntegrationTestCase
{
    public function testFetchAllMenus(): void
    {
        $menus = $this->client->menus()->all();

        $this->assertInstanceOf(PaginatedResponse::class, $menus);
        $this->assertIsArray($menus->getData());
    }

    public function testFetchMenuById(): void
    {
        $menus = $this->client->menus()->all();

        if (empty($menus->getData())) {
            $this->markTestSkipped('No menus available in WordPress. Create one in WordPress admin (Appearance > Menus)');
        }

        $firstMenu = $menus->getData()[0];
        $menuId = $firstMenu['id'];

        $menu = $this->client->menus()->get($menuId);

        $this->assertIsArray($menu);
        $this->assertEquals($menuId, $menu['id']);
        $this->assertArrayHasKey('name', $menu);
    }

    public function testFetchMenuBySlug(): void
    {
        $menus = $this->client->menus()->all();

        if (empty($menus->getData())) {
            $this->markTestSkipped('No menus available in WordPress. Create one in WordPress admin (Appearance > Menus)');
        }

        $slug = $menus->getData()[0]['slug'];
        $menu = $this->client->menus()->getBySlug($slug);

        $this->assertIsArray($menu);
        $this->assertEquals($slug, $menu['slug']);
    }

    public function testFetchMenuByInvalidSlugThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->menus()->getBySlug('non-existent-menu-slug-12345');
    }

    public function testGetMenuByLocationReturnsNullWhenUnassigned(): void
    {
        $menu = $this->client->menus()->getByLocation('non-existent-location-12345');

        $this->assertNull($menu);
    }

    public function testFetchMenuItems(): void
    {
        $menus = $this->client->menus()->all();

        if (empty($menus->getData())) {
            $this->markTestSkipped('No menus available in WordPress. Create one in WordPress admin (Appearance > Menus)');
        }

        $menuId = $menus->getData()[0]['id'];
        $items = $this->client->menus()->getItems($menuId);

        $this->assertIsArray($items);
    }

    public function testGetMenuWithItems(): void
    {
        $menus = $this->client->menus()->all();

        if (empty($menus->getData())) {
            $this->markTestSkipped('No menus available in WordPress. Create one in WordPress admin (Appearance > Menus)');
        }

        $menuId = $menus->getData()[0]['id'];
        $result = $this->client->menus()->getWithItems($menuId);

        $this->assertArrayHasKey('menu', $result);
        $this->assertArrayHasKey('items', $result);
        $this->assertEquals($menuId, $result['menu']['id']);
    }

    public function testBuildHierarchyFromRealMenuItems(): void
    {
        $menus = $this->client->menus()->all();

        if (empty($menus->getData())) {
            $this->markTestSkipped('No menus available in WordPress. Create one in WordPress admin (Appearance > Menus)');
        }

        $menuId = $menus->getData()[0]['id'];
        $items = $this->client->menus()->getItems($menuId);

        $hierarchy = $this->client->menus()->buildHierarchy($items);

        $this->assertIsArray($hierarchy);
    }
}
