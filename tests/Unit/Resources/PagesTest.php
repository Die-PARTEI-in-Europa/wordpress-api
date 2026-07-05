<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Resources;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Resources\Pages;
use WordPressApi\Support\QueryBuilder;

class PagesTest extends TestCase
{
    private Pages $pages;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->pages = new Pages($this->client, 'https://example.com/wp-json', '/wp/v2/pages');
    }

    public function testPagesInstantiation(): void
    {
        $this->assertInstanceOf(Pages::class, $this->pages);
    }

    public function testSearchReturnsQueryBuilder(): void
    {
        $result = $this->pages->search('wordpress');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testLanguageReturnsQueryBuilder(): void
    {
        $result = $this->pages->language('de');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testWhereReturnsQueryBuilder(): void
    {
        $result = $this->pages->where('status', 'publish');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testOrderByReturnsQueryBuilder(): void
    {
        $result = $this->pages->orderBy('title', 'asc');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPerPageReturnsQueryBuilder(): void
    {
        $result = $this->pages->perPage(20);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPageReturnsQueryBuilder(): void
    {
        $result = $this->pages->page(2);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testGetBySlugMethodExists(): void
    {
        $this->assertTrue(method_exists($this->pages, 'getBySlug'));
    }
}
