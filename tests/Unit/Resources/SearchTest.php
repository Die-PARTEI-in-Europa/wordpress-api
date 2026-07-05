<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Resources;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Resources\Search;
use WordPressApi\Support\QueryBuilder;

class SearchTest extends TestCase
{
    private Search $search;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->search = new Search($this->client, 'https://example.com/wp-json', '/wp/v2/search');
    }

    public function testSearchInstantiation(): void
    {
        $this->assertInstanceOf(Search::class, $this->search);
    }

    public function testSearchTermReturnsQueryBuilder(): void
    {
        $result = $this->search->search('wordpress');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testTypeReturnsQueryBuilder(): void
    {
        $result = $this->search->type('post');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testSubtypeReturnsQueryBuilder(): void
    {
        $result = $this->search->subtype('page');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testLanguageReturnsQueryBuilder(): void
    {
        $result = $this->search->language('de');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPerPageReturnsQueryBuilder(): void
    {
        $result = $this->search->perPage(10);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testPageReturnsQueryBuilder(): void
    {
        $result = $this->search->page(2);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testTypeAndSubtypeCanBeCombined(): void
    {
        $result = $this->search->type('post')->where('subtype', 'post');
        $this->assertInstanceOf(QueryBuilder::class, $result);
        $this->assertEquals('post', $result->getParams()['subtype']);
    }
}
