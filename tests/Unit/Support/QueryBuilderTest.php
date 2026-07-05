<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Support;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Support\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    private QueryBuilder $builder;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->builder = new QueryBuilder($this->client, '/wp/v2/posts');
    }

    public function testWhereAddsParameter(): void
    {
        $this->builder->where('status', 'publish');
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('status', $params);
        $this->assertEquals('publish', $params['status']);
    }

    public function testLanguageAddsLangParameter(): void
    {
        $this->builder->language('de');
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('lang', $params);
        $this->assertEquals('de', $params['lang']);
    }

    public function testOrderByAddsParameters(): void
    {
        $this->builder->orderBy('date', 'desc');
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('orderby', $params);
        $this->assertArrayHasKey('order', $params);
        $this->assertEquals('date', $params['orderby']);
        $this->assertEquals('desc', $params['order']);
    }

    public function testPerPageAddsParameter(): void
    {
        $this->builder->perPage(20);
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('per_page', $params);
        $this->assertEquals(20, $params['per_page']);
    }

    public function testPageAddsParameter(): void
    {
        $this->builder->page(2);
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('page', $params);
        $this->assertEquals(2, $params['page']);
    }

    public function testSearchAddsParameter(): void
    {
        $this->builder->search('wordpress');
        $params = $this->builder->getParams();

        $this->assertArrayHasKey('search', $params);
        $this->assertEquals('wordpress', $params['search']);
    }

    public function testFluentInterface(): void
    {
        $result = $this->builder
            ->where('status', 'publish')
            ->language('de')
            ->search('test')
            ->orderBy('date', 'desc')
            ->perPage(10)
            ->page(1);

        $this->assertInstanceOf(QueryBuilder::class, $result);

        $params = $result->getParams();
        $this->assertEquals('publish', $params['status']);
        $this->assertEquals('de', $params['lang']);
        $this->assertEquals('test', $params['search']);
        $this->assertEquals('date', $params['orderby']);
        $this->assertEquals('desc', $params['order']);
        $this->assertEquals(10, $params['per_page']);
        $this->assertEquals(1, $params['page']);
    }

    public function testMultipleLanguages(): void
    {
        $this->builder->language('en');
        $params = $this->builder->getParams();
        $this->assertEquals('en', $params['lang']);

        $this->builder->language('fr');
        $params = $this->builder->getParams();
        $this->assertEquals('fr', $params['lang']);
    }
}
