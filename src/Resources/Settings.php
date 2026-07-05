<?php

declare(strict_types=1);

namespace WordPressApi\Resources;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use WordPressApi\Exceptions\AuthenticationException;
use WordPressApi\Exceptions\WordPressApiException;

/**s 2>/dev/null && echo "Host key added")
 * WordPress Settings Resource
 *
 * Note: Some settings require authentication to read.
 * Public settings are available without authentication.
 */
class Settings
{
    private GuzzleClient $client;
    private string $endpoint;

    public function __construct(GuzzleClient $client, string $baseUrl)
    {
        $this->client = $client;
        $this->endpoint = rtrim($baseUrl, '/') . '/wp/v2/settings';
    }

    /**
     * Get all WordPress settings
     *
     * Note: Requires authentication for most settings.
     * Some basic settings may be publicly available.
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     * @throws WordPressApiException
     */
    public function all(): array
    {
        try {
            $response = $this->client->get($this->endpoint);
            return json_decode((string) $response->getBody(), true) ?? [];
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                throw new AuthenticationException(
                    "Authentication required to access settings",
                    $e->getResponse()->getStatusCode(),
                    $e
                );
            }
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get site title
     */
    public function getTitle(): ?string
    {
        $settings = $this->all();
        return $settings['title'] ?? null;
    }

    /**
     * Get site description/tagline
     */
    public function getDescription(): ?string
    {
        $settings = $this->all();
        return $settings['description'] ?? null;
    }

    /**
     * Get site URL
     */
    public function getUrl(): ?string
    {
        $settings = $this->all();
        return $settings['url'] ?? null;
    }

    /**
     * Get site email
     */
    public function getEmail(): ?string
    {
        $settings = $this->all();
        return $settings['email'] ?? null;
    }

    /**
     * Get timezone string
     */
    public function getTimezone(): ?string
    {
        $settings = $this->all();
        return $settings['timezone'] ?? null;
    }

    /**
     * Get date format
     */
    public function getDateFormat(): ?string
    {
        $settings = $this->all();
        return $settings['date_format'] ?? null;
    }

    /**
     * Get time format
     */
    public function getTimeFormat(): ?string
    {
        $settings = $this->all();
        return $settings['time_format'] ?? null;
    }

    /**
     * Get site language
     */
    public function getLanguage(): ?string
    {
        $settings = $this->all();
        return $settings['language'] ?? null;
    }

    /**
     * Get posts per page setting
     */
    public function getPostsPerPage(): ?int
    {
        $settings = $this->all();
        return isset($settings['posts_per_page']) ? (int) $settings['posts_per_page'] : null;
    }

    /**
     * Get front page ID
     *
     * Returns the page ID set as the front page.
     * Returns 0 if posts are shown on front page.
     */
    public function getFrontPageId(): int
    {
        $settings = $this->all();
        return isset($settings['page_on_front']) ? (int) $settings['page_on_front'] : 0;
    }

    /**
     * Get posts page ID
     *
     * Returns the page ID set as the posts page.
     * Returns 0 if no specific page is set.
     */
    public function getPostsPageId(): int
    {
        $settings = $this->all();
        return isset($settings['page_for_posts']) ? (int) $settings['page_for_posts'] : 0;
    }

    /**
     * Check if a static page is set as front page
     */
    public function hasStaticFrontPage(): bool
    {
        return $this->getFrontPageId() > 0;
    }

    /**
     * Get what's shown on the front page
     *
     * Returns 'posts' or 'page'
     */
    public function getShowOnFront(): string
    {
        $settings = $this->all();
        return $settings['show_on_front'] ?? 'posts';
    }

    /**
     * Check if site uses default permalink structure
     */
    public function usesDefaultPermalinks(): bool
    {
        $settings = $this->all();
        $structure = $settings['permalink_structure'] ?? '';
        return empty($structure);
    }

    /**
     * Get permalink structure
     */
    public function getPermalinkStructure(): string
    {
        $settings = $this->all();
        return $settings['permalink_structure'] ?? '';
    }
}
