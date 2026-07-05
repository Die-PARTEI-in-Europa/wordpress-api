<?php

declare(strict_types=1);

namespace WordPressApi;

use GuzzleHttp\Client as GuzzleClient;
use WordPressApi\Resources\Posts;
use WordPressApi\Resources\Pages;
use WordPressApi\Resources\Menus;
use WordPressApi\Resources\Search;
use WordPressApi\Resources\Homepage;
use WordPressApi\Resources\Settings;

class Client
{
    private GuzzleClient $httpClient;
    private string $baseUrl;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $baseUrl, array $options = [])
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/wp-json';

        $config = [
            'base_uri' => $this->baseUrl,
            'timeout' => $options['timeout'] ?? 30,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        // Add authentication if provided
        if (isset($options['auth'])) {
            $config['auth'] = $options['auth'];
        }

        $this->httpClient = new GuzzleClient($config);
    }

    /**
     * Get Posts resource
     */
    public function posts(): Posts
    {
        return new Posts($this->httpClient, $this->baseUrl, '/wp/v2/posts');
    }

    /**
     * Get Pages resource
     */
    public function pages(): Pages
    {
        return new Pages($this->httpClient, $this->baseUrl, '/wp/v2/pages');
    }

    /**
     * Get Menus resource
     */
    public function menus(): Menus
    {
        return new Menus($this->httpClient, $this->baseUrl, '/wp/v2/menus');
    }

    /**
     * Get Search resource
     */
    public function search(): Search
    {
        return new Search($this->httpClient, $this->baseUrl, '/wp/v2/search');
    }

    /**
     * Get homepage (front page)
     *
     * Note: This is a convenience method. WordPress homepage can be:
     * - A static page (check Settings > Reading in WordPress admin)
     * - The blog posts list
     *
     * For more control, query pages directly or check site settings.
     */
    public function homepage(): Homepage
    {
        return new Homepage(
            $this->pages(),
            $this->posts(),
            $this->settings()
        );
    }

    /**
     * Get WordPress settings
     *
     * Note: Most settings require authentication to read.
     * Some basic settings may be publicly available.
     */
    public function settings(): Settings
    {
        return new Settings($this->httpClient, $this->baseUrl);
    }

    /**
     * Get the HTTP client
     */
    public function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    /**
     * Get the base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
