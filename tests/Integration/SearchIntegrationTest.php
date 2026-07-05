<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use WordPressApi\Support\PaginatedResponse;

class SearchIntegrationTest extends IntegrationTestCase
{
    public function testGlobalSearch(): void
    {
        $results = $this->client->search()
            ->search('test')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
    }

    public function testSearchWithType(): void
    {
        $results = $this->client->search()
            ->search('test')
            ->type('post')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());

        // Verify all results are posts (if any)
        foreach ($results->getData() as $item) {
            $this->assertEquals('post', $item['type']);
        }
    }

    public function testSearchWithPagination(): void
    {
        $page1 = $this->client->search()
            ->search('test')
            ->perPage(5)
            ->page(1)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $page1);
        $this->assertEquals(1, $page1->getCurrentPage());
        $this->assertLessThanOrEqual(5, count($page1->getData()));
    }

    public function testSearchWithLanguageFilter(): void
    {
        // This test will pass even without WPML installed
        $results = $this->client->search()
            ->search('test')
            ->language('en')
            ->perPage(10)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
    }

    public function testSearchPosts(): void
    {
        $results = $this->client->search()
            ->type('post')
            ->search('test')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
    }

    public function testSearchPages(): void
    {
        $results = $this->client->search()
            ->subtype('page')  // WordPress Search API uses 'subtype' for post types
            ->search('test')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
    }

    public function testEmptySearchQuery(): void
    {
        $results = $this->client->search()
            ->search('')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
    }

    public function testSearchWithNoResults(): void
    {
        $results = $this->client->search()
            ->search('xyznonexistentterm12345')
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
        $this->assertEquals(0, $results->getTotal());
    }

    public function testSearchCombinedFilters(): void
    {
        $results = $this->client->search()
            ->search('test')
            ->type('post')
            ->perPage(3)
            ->page(1)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $results);
        $this->assertIsArray($results->getData());
        $this->assertLessThanOrEqual(3, count($results->getData()));
    }
}
