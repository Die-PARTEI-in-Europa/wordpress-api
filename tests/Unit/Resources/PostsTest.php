<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Resources;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Resources\Posts;
use WordPressApi\Support\QueryBuilder;

class PostsTest extends TestCase
{
    private Posts $posts;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->posts = new Posts($this->client, 'https://example.com/wp-json', '/wp/v2/posts');
    }

    public function testPostsInstantiation(): void
    {
        $this->assertInstanceOf(Posts::class, $this->posts);
    }

    public function testSearchReturnsQueryBuilder(): void
    {
        $result = $this->posts->search('wordpress');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testLanguageReturnsQueryBuilder(): void
    {
        $result = $this->posts->language('de');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testWhereReturnsQueryBuilder(): void
    {
        $result = $this->posts->where('status', 'publish');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testOrderByReturnsQueryBuilder(): void
    {
        $result = $this->posts->orderBy('date', 'desc');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPerPageReturnsQueryBuilder(): void
    {
        $result = $this->posts->perPage(20);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPageReturnsQueryBuilder(): void
    {
        $result = $this->posts->page(2);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testGetBySlugMethodExists(): void
    {
        $this->assertTrue(method_exists($this->posts, 'getBySlug'));
    }
}
