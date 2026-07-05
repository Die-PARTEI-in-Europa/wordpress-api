<?php

declare(strict_types=1);

namespace WordPressApi\Resources;

use WordPressApi\Exceptions\AuthenticationException;
use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Exceptions\WordPressApiException;

class Homepage
{
    private Pages $pages;
    private Posts $posts;
    private ?Settings $settings;

    public function __construct(Pages $pages, Posts $posts, ?Settings $settings = null)
    {
        $this->pages = $pages;
        $this->posts = $posts;
        $this->settings = $settings;
    }

    /**
     * Get the homepage content
     *
     * This checks WordPress settings to determine what the homepage is:
     * - If a static page is set as homepage, returns that page
     * - Otherwise returns latest posts
     *
     * @return array<string, mixed>
     * @throws NotFoundException
     * @throws WordPressApiException
     */
    public function get(): array
    {
        // Try to get homepage settings from WordPress
        if ($this->settings !== null) {
            try {
                $frontPageId = $this->settings->getFrontPageId();

                // If a static page is set as front page
                if ($frontPageId > 0) {
                    $page = $this->pages->get($frontPageId);
                    return [
                        'type' => 'page',
                        'page' => $page,
                    ];
                }
            } catch (AuthenticationException $e) {
                // Settings require auth; WordPress' default is posts-on-front,
                // so fall through to the posts list. Connection/server errors
                // are NOT swallowed here — they propagate so the caller sees
                // the real failure instead of unexpected content.
            }
        }

        // If no specific homepage is set, WordPress shows latest posts
        $postsPerPage = 10;

        // Try to get posts_per_page setting if Settings is available
        if ($this->settings !== null) {
            try {
                $postsPerPage = $this->settings->getPostsPerPage() ?? 10;
            } catch (AuthenticationException $e) {
                // Settings not readable without auth — keep the default of 10.
            }
        }

        $latestPosts = $this->posts->perPage($postsPerPage)->get();

        return [
            'type' => 'posts_list',
            'message' => 'Homepage shows latest blog posts',
            'posts' => $latestPosts->getData(),
            'total_posts' => $latestPosts->getTotal(),
        ];
    }
}
