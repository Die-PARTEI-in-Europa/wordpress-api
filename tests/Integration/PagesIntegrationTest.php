<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Support\PaginatedResponse;

class PagesIntegrationTest extends IntegrationTestCase
{
    /**
     * Test data seeder instance
     */
    private TestDataSeeder $seeder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seeder = new TestDataSeeder($this->client);
    }
    public function testFetchAllPages(): void
    {
        $pages = $this->client->pages()->all();

        $this->assertInstanceOf(PaginatedResponse::class, $pages);
        $this->assertIsArray($pages->getData());
    }

    public function testFetchPageById(): void
    {
        $pages = $this->client->pages()->all();

        if (empty($pages->getData())) {
            $this->markTestSkipped('No pages available in WordPress');
        }

        $firstPage = $pages->getData()[0];
        $pageId = $firstPage['id'];

        $page = $this->client->pages()->get($pageId);

        $this->assertIsArray($page);
        $this->assertEquals($pageId, $page['id']);
        $this->assertArrayHasKey('title', $page);
        $this->assertArrayHasKey('content', $page);
    }

    public function testFetchPageBySlug(): void
    {
        // Use known test data
        $slug = TestDataSeeder::getTestPageSlug();
        $page = $this->client->pages()->getBySlug($slug);

        $this->assertIsArray($page);
        $this->assertEquals($slug, $page['slug']);
        $this->assertArrayHasKey('title', $page);

        // Verify expected data
        $expectedData = TestDataSeeder::getTestPageData($slug);
        if ($expectedData) {
            $this->assertEquals($expectedData['title'], $page['title']['rendered']);
        }
    }

    public function testFetchPageByInvalidSlugThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->pages()->getBySlug('non-existent-page-slug-12345');
    }

    public function testQueryPagesWithFilters(): void
    {
        $pages = $this->client->pages()
            ->where('status', 'publish')
            ->orderBy('title', 'asc')
            ->perPage(5)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $pages);
        $this->assertIsArray($pages->getData());
    }

    public function testSearchPages(): void
    {
        $pages = $this->client->pages()
            ->search('test')
            ->perPage(10)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $pages);
        $this->assertIsArray($pages->getData());
    }

    public function testLanguageFilterOnPages(): void
    {
        // Test language parameter (works with WPML/Polylang)
        $pages = $this->client->pages()
            ->language('en')
            ->perPage(5)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $pages);
        $this->assertIsArray($pages->getData());
    }

    public function testFetchMultilingualPageEnglish(): void
    {
        // Test fetching English page
        $slug = TestDataSeeder::getMultilingualPageSlug('en');
        if (!$slug) {
            $this->markTestSkipped('No English multilingual test page defined');
        }

        try {
            $page = $this->client->pages()->getBySlug($slug);
            $this->assertIsArray($page);
            $this->assertEquals($slug, $page['slug']);
        } catch (NotFoundException $e) {
            $this->markTestSkipped('Multilingual test page not found. Polylang may not be configured.');
        }
    }

    public function testFetchMultilingualPageGerman(): void
    {
        // Test fetching German page
        $slug = TestDataSeeder::getMultilingualPageSlug('de');
        if (!$slug) {
            $this->markTestSkipped('No German multilingual test page defined');
        }

        try {
            $page = $this->client->pages()->getBySlug($slug);
            $this->assertIsArray($page);
            $this->assertEquals($slug, $page['slug']);
        } catch (NotFoundException $e) {
            $this->markTestSkipped('German test page not found. WPML may not be configured.');
        }
    }

    public function testPagesPagination(): void
    {
        $page1 = $this->client->pages()
            ->perPage(3)
            ->page(1)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $page1);
        $this->assertEquals(1, $page1->getCurrentPage());

        // Only test pagination if there are enough pages
        if ($page1->hasNextPage()) {
            $page2 = $page1->nextPage();
            $this->assertEquals(2, $page2->getCurrentPage());
        }
    }
}
