<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Support\PaginatedResponse;

class PostsIntegrationTest extends IntegrationTestCase
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
    public function testFetchAllPosts(): void
    {
        $posts = $this->client->posts()->all();

        $this->assertInstanceOf(PaginatedResponse::class, $posts);
        $this->assertGreaterThan(0, $posts->getTotal());
        $this->assertIsArray($posts->getData());
    }

    public function testFetchPostById(): void
    {
        // First get all posts to get a valid ID
        $posts = $this->client->posts()->all();
        $this->assertNotEmpty($posts->getData());

        $firstPost = $posts->getData()[0];
        $postId = $firstPost['id'];

        // Now fetch by ID
        $post = $this->client->posts()->get($postId);

        $this->assertIsArray($post);
        $this->assertEquals($postId, $post['id']);
        $this->assertArrayHasKey('title', $post);
        $this->assertArrayHasKey('content', $post);
    }

    public function testFetchPostBySlug(): void
    {
        // Use known test data
        $slug = TestDataSeeder::getTestPostSlug();
        $post = $this->client->posts()->getBySlug($slug);

        $this->assertIsArray($post);
        $this->assertEquals($slug, $post['slug']);
        $this->assertArrayHasKey('title', $post);

        // Verify expected data
        $expectedData = TestDataSeeder::getTestPostData($slug);
        if ($expectedData) {
            $this->assertEquals($expectedData['title'], $post['title']['rendered']);
        }
    }

    public function testFetchPostByInvalidSlugThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->posts()->getBySlug('non-existent-slug-12345');
    }

    public function testFetchPostByInvalidIdThrowsException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->client->posts()->get(999999);
    }

    public function testQueryPostsWithFilters(): void
    {
        $posts = $this->client->posts()
            ->where('status', 'publish')
            ->orderBy('date', 'desc')
            ->perPage(5)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $posts);
        $this->assertLessThanOrEqual(5, count($posts->getData()));
    }

    public function testSearchPosts(): void
    {
        $posts = $this->client->posts()
            ->search('test')
            ->perPage(10)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $posts);
        $this->assertIsArray($posts->getData());
    }

    public function testPagination(): void
    {
        $page1 = $this->client->posts()
            ->perPage(5)
            ->page(1)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $page1);
        $this->assertEquals(1, $page1->getCurrentPage());

        // Only test next page if there are enough posts
        if ($page1->hasNextPage()) {
            $page2 = $page1->nextPage();
            $this->assertEquals(2, $page2->getCurrentPage());
            $this->assertNotEquals($page1->getData(), $page2->getData());
        }
    }

    public function testOrderBy(): void
    {
        $postsAsc = $this->client->posts()
            ->orderBy('title', 'asc')
            ->perPage(5)
            ->get();

        $postsDesc = $this->client->posts()
            ->orderBy('title', 'desc')
            ->perPage(5)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $postsAsc);
        $this->assertInstanceOf(PaginatedResponse::class, $postsDesc);

        // Verify they're different (unless there's only 1 post)
        if (count($postsAsc->getData()) > 1) {
            $this->assertNotEquals(
                $postsAsc->getData()[0]['title']['rendered'],
                $postsDesc->getData()[0]['title']['rendered']
            );
        }
    }

    public function testLanguageFilter(): void
    {
        // Test language parameter (works with WPML/Polylang)
        $posts = $this->client->posts()
            ->language('en')
            ->perPage(5)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $posts);
        $this->assertIsArray($posts->getData());
    }

    public function testFetchMultilingualPostEnglish(): void
    {
        // Test fetching English post
        $slug = TestDataSeeder::getMultilingualPostSlug('en');
        if (!$slug) {
            $this->markTestSkipped('No English multilingual test post defined');
        }

        try {
            $post = $this->client->posts()->getBySlug($slug);
            $this->assertIsArray($post);
            $this->assertEquals($slug, $post['slug']);
        } catch (NotFoundException $e) {
            $this->markTestSkipped('Multilingual test post not found. Polylang may not be configured.');
        }
    }

    public function testFetchMultilingualPostGerman(): void
    {
        // Test fetching German post
        $slug = TestDataSeeder::getMultilingualPostSlug('de');
        if (!$slug) {
            $this->markTestSkipped('No German multilingual test post defined');
        }

        try {
            $post = $this->client->posts()->getBySlug($slug);
            $this->assertIsArray($post);
            $this->assertEquals($slug, $post['slug']);
        } catch (NotFoundException $e) {
            $this->markTestSkipped('German test post not found. WPML may not be configured.');
        }
    }

    public function testLanguageFilterWithPolylang(): void
    {
        // Test Polylang language filtering
        // This will work if Polylang is configured, otherwise just verify the parameter is accepted
        $germanPosts = $this->client->posts()
            ->language('de')
            ->perPage(10)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $germanPosts);

        // If Polylang is configured and we have German posts, verify they're returned
        // Otherwise, this just confirms the API accepts the parameter
        if ($germanPosts->getTotal() > 0) {
            $this->assertGreaterThan(0, count($germanPosts->getData()));
        }
    }

    public function testCombinedFilters(): void
    {
        $posts = $this->client->posts()
            ->where('status', 'publish')
            ->orderBy('date', 'desc')
            ->perPage(3)
            ->page(1)
            ->get();

        $this->assertInstanceOf(PaginatedResponse::class, $posts);
        $this->assertLessThanOrEqual(3, count($posts->getData()));
    }
}
