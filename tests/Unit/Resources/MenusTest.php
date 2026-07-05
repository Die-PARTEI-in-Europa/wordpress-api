<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Resources;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Resources\Menus;

class MenusTest extends TestCase
{
    private Menus $menus;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->menus = new Menus($this->client, 'https://example.com/wp-json', '/wp/v2/menus');
    }

    public function testMenusInstantiation(): void
    {
        $this->assertInstanceOf(Menus::class, $this->menus);
    }

    public function testGetItemsMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'getItems'));
    }

    public function testGetBySlugMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'getBySlug'));
    }

    public function testGetByLocationMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'getByLocation'));
    }

    public function testGetWithItemsMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'getWithItems'));
    }

    public function testGetBySlugWithItemsMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'getBySlugWithItems'));
    }

    public function testBuildHierarchyMethodExists(): void
    {
        $this->assertTrue(method_exists($this->menus, 'buildHierarchy'));
    }

    public function testBuildHierarchyReturnsArray(): void
    {
        // Test with empty array
        $result = $this->menus->buildHierarchy([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testBuildHierarchyWithFlatItems(): void
    {
        $items = [
            ['id' => 1, 'parent' => 0, 'title' => 'Home'],
            ['id' => 2, 'parent' => 0, 'title' => 'About'],
            ['id' => 3, 'parent' => 2, 'title' => 'Team'],
        ];

        $hierarchy = $this->menus->buildHierarchy($items);

        $this->assertIsArray($hierarchy);
        $this->assertCount(2, $hierarchy); // Two top-level items
        $this->assertEquals('Home', $hierarchy[0]['title']);
        $this->assertEquals('About', $hierarchy[1]['title']);
        $this->assertCount(1, $hierarchy[1]['children']); // About has one child
        $this->assertEquals('Team', $hierarchy[1]['children'][0]['title']);
    }

    public function testBuildHierarchyWithMultipleLevels(): void
    {
        $items = [
            ['id' => 1, 'parent' => 0, 'title' => 'Home'],
            ['id' => 2, 'parent' => 0, 'title' => 'About'],
            ['id' => 3, 'parent' => 2, 'title' => 'Team'],
            ['id' => 4, 'parent' => 3, 'title' => 'Leadership'],
        ];

        $hierarchy = $this->menus->buildHierarchy($items);

        $this->assertIsArray($hierarchy);
        $this->assertCount(2, $hierarchy);
        $this->assertCount(1, $hierarchy[1]['children']);
        $this->assertCount(1, $hierarchy[1]['children'][0]['children']);
        $this->assertEquals('Leadership', $hierarchy[1]['children'][0]['children'][0]['title']);
    }
}
