<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use WordPressApi\Client;

/**
 * Manages test data for integration tests
 */
class TestDataSeeder
{
    private Client $client;
    private array $createdPostIds = [];
    private array $createdPageIds = [];

    /**
     * Known test data slugs that should exist in WordPress
     */
    public const TEST_POSTS = [
        'sample-post' => [
            'title' => 'Sample Post',
            'content' => 'This is a sample post for testing the WordPress API SDK.',
            'status' => 'publish',
        ],
        'draft-post' => [
            'title' => 'Draft Post',
            'content' => 'This is a draft post.',
            'status' => 'draft',
        ],
        'test-pagination-1' => [
            'title' => 'Test Pagination 1',
            'content' => 'First post for pagination testing.',
            'status' => 'publish',
        ],
        'test-pagination-2' => [
            'title' => 'Test Pagination 2',
            'content' => 'Second post for pagination testing.',
            'status' => 'publish',
        ],
        'test-pagination-3' => [
            'title' => 'Test Pagination 3',
            'content' => 'Third post for pagination testing.',
            'status' => 'publish',
        ],
    ];

    public const TEST_PAGES = [
        'sample-page' => [
            'title' => 'Sample Page',
            'content' => 'This is a sample page.',
            'status' => 'publish',
        ],
        'about-us' => [
            'title' => 'About Us',
            'content' => 'Learn more about our company.',
            'status' => 'publish',
        ],
        'contact' => [
            'title' => 'Contact',
            'content' => 'Get in touch with us.',
            'status' => 'publish',
        ],
    ];

    /**
     * Multilingual test posts (Polylang)
     */
    public const MULTILINGUAL_POSTS = [
        'en' => [
            'hello-world-en' => [
                'title' => 'Hello World English',
                'content' => 'Welcome to WordPress. This is your first post in English.',
                'language' => 'en',
            ],
            'technology-post-en' => [
                'title' => 'Technology Post',
                'content' => 'This is a post about technology in English.',
                'language' => 'en',
            ],
        ],
        'de' => [
            'hallo-welt' => [
                'title' => 'Hallo Welt',
                'content' => 'Willkommen bei WordPress. Dies ist dein erster Beitrag auf Deutsch.',
                'language' => 'de',
            ],
            'technologie-beitrag' => [
                'title' => 'Technologie-Beitrag',
                'content' => 'Dies ist ein Beitrag über Technologie auf Deutsch.',
                'language' => 'de',
            ],
        ],
        'fr' => [
            'bonjour-le-monde' => [
                'title' => 'Bonjour le monde',
                'content' => 'Bienvenue sur WordPress. Ceci est votre premier article en français.',
                'language' => 'fr',
            ],
        ],
    ];

    /**
     * Multilingual test pages (Polylang)
     */
    public const MULTILINGUAL_PAGES = [
        'en' => [
            'about-us-en' => [
                'title' => 'About Us',
                'content' => 'Learn more about our company.',
                'language' => 'en',
            ],
        ],
        'de' => [
            'ueber-uns' => [
                'title' => 'Über uns',
                'content' => 'Erfahren Sie mehr über unser Unternehmen.',
                'language' => 'de',
            ],
        ],
    ];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a known test post slug
     */
    public static function getTestPostSlug(): string
    {
        return array_key_first(self::TEST_POSTS);
    }

    /**
     * Get a known test page slug
     */
    public static function getTestPageSlug(): string
    {
        return array_key_first(self::TEST_PAGES);
    }

    /**
     * Get all test post slugs
     *
     * @return array<string>
     */
    public static function getTestPostSlugs(): array
    {
        return array_keys(self::TEST_POSTS);
    }

    /**
     * Get all test page slugs
     *
     * @return array<string>
     */
    public static function getTestPageSlugs(): array
    {
        return array_keys(self::TEST_PAGES);
    }

    /**
     * Get test post data by slug
     *
     * @return array<string, mixed>|null
     */
    public static function getTestPostData(string $slug): ?array
    {
        return self::TEST_POSTS[$slug] ?? null;
    }

    /**
     * Get test page data by slug
     *
     * @return array<string, mixed>|null
     */
    public static function getTestPageData(string $slug): ?array
    {
        return self::TEST_PAGES[$slug] ?? null;
    }

    /**
     * Get multilingual post slugs by language
     *
     * @return array<string>
     */
    public static function getMultilingualPostSlugs(string $language): array
    {
        return array_keys(self::MULTILINGUAL_POSTS[$language] ?? []);
    }

    /**
     * Get multilingual page slugs by language
     *
     * @return array<string>
     */
    public static function getMultilingualPageSlugs(string $language): array
    {
        return array_keys(self::MULTILINGUAL_PAGES[$language] ?? []);
    }

    /**
     * Get available test languages
     *
     * @return array<string>
     */
    public static function getAvailableLanguages(): array
    {
        return ['en', 'de', 'fr'];
    }

    /**
     * Get a test post slug for a specific language
     */
    public static function getMultilingualPostSlug(string $language): ?string
    {
        $slugs = self::getMultilingualPostSlugs($language);
        return !empty($slugs) ? $slugs[0] : null;
    }

    /**
     * Get a test page slug for a specific language
     */
    public static function getMultilingualPageSlug(string $language): ?string
    {
        $slugs = self::getMultilingualPageSlugs($language);
        return !empty($slugs) ? $slugs[0] : null;
    }

    /**
     * Verify that expected test data exists in WordPress
     *
     * @return array{posts: int, pages: int, missing_posts: array, missing_pages: array}
     */
    public function verifyTestData(): array
    {
        $missingPosts = [];
        $missingPages = [];
        $foundPosts = 0;
        $foundPages = 0;

        // Check posts
        foreach (self::TEST_POSTS as $slug => $data) {
            try {
                $this->client->posts()->getBySlug($slug);
                $foundPosts++;
            } catch (\Exception $e) {
                $missingPosts[] = $slug;
            }
        }

        // Check pages
        foreach (self::TEST_PAGES as $slug => $data) {
            try {
                $this->client->pages()->getBySlug($slug);
                $foundPages++;
            } catch (\Exception $e) {
                $missingPages[] = $slug;
            }
        }

        return [
            'posts' => $foundPosts,
            'pages' => $foundPages,
            'missing_posts' => $missingPosts,
            'missing_pages' => $missingPages,
        ];
    }
}
