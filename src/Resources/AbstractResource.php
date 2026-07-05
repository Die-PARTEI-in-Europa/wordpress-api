<?php

declare(strict_types=1);

namespace WordPressApi\Resources;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use WordPressApi\Contracts\ResourceInterface;
use WordPressApi\Exceptions\AuthenticationException;
use WordPressApi\Exceptions\NotFoundException;
use WordPressApi\Exceptions\WordPressApiException;
use WordPressApi\Support\PaginatedResponse;
use WordPressApi\Support\QueryBuilder;

abstract class AbstractResource implements ResourceInterface
{
    protected GuzzleClient $client;
    protected string $endpoint;
    protected string $baseUrl;

    public function __construct(GuzzleClient $client, string $baseUrl, string $endpoint)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->endpoint = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Get the HTTP client
     */
    public function getHttpClient(): GuzzleClient
    {
        return $this->client;
    }

    /**
     * Get the base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Fetch all items with optional parameters
     *
     * @param array<string, mixed> $params
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->query($params);
    }

    /**
     * Fetch a single item by ID
     *
     * @return array<string, mixed>
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws WordPressApiException
     */
    public function get(int $id): array
    {
        try {
            $response = $this->client->get("{$this->endpoint}/{$id}");
            $data = json_decode((string) $response->getBody(), true);

            // Handle empty or invalid JSON response
            if ($data === null || !is_array($data)) {
                throw new NotFoundException("Resource with ID {$id} not found or returned invalid data", 404);
            }

            return $data;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFoundException("Resource with ID {$id} not found", 404, $e);
            }
            if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                throw new AuthenticationException("Authentication failed", $e->getResponse()->getStatusCode(), $e);
            }
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Fetch a single item by slug
     *
     * @return array<string, mixed>
     * @throws NotFoundException
     * @throws AuthenticationException
     * @throws WordPressApiException
     */
    public function getBySlug(string $slug): array
    {
        try {
            $response = $this->client->get($this->endpoint, ['query' => ['slug' => $slug]]);
            $data = json_decode((string) $response->getBody(), true);

            if (empty($data) || !is_array($data)) {
                throw new NotFoundException("Resource with slug '{$slug}' not found", 404);
            }

            // WordPress REST API returns an array of items when filtering by slug
            // We return the first (and should be only) match
            return $data[0];
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                throw new AuthenticationException("Authentication failed", $e->getResponse()->getStatusCode(), $e);
            }
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Custom query with parameters
     *
     * @param array<string, mixed> $params
     * @throws WordPressApiException
     */
    public function query(array $params): PaginatedResponse
    {
        try {
            $response = $this->client->get($this->endpoint, ['query' => $params]);
            $data = json_decode((string) $response->getBody(), true);

            // Handle empty or invalid JSON response
            if ($data === null || !is_array($data)) {
                $data = [];
            }

            return new PaginatedResponse(
                $response,
                $data,
                $this->client,
                $this->endpoint,
                $params
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401 || $e->getResponse()->getStatusCode() === 403) {
                throw new AuthenticationException("Authentication failed", $e->getResponse()->getStatusCode(), $e);
            }
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Search within the resource
     */
    public function search(string $term): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->search($term);
    }

    /**
     * Filter by language (WPML support)
     */
    public function language(string $languageCode): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->language($languageCode);
    }

    /**
     * Start building a query
     *
     * @param mixed $value
     */
    public function where(string $key, $value): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->where($key, $value);
    }

    /**
     * Set order by
     */
    public function orderBy(string $field, string $direction = 'asc'): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->orderBy($field, $direction);
    }

    /**
     * Set results per page
     */
    public function perPage(int $perPage): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->perPage($perPage);
    }

    /**
     * Set page number
     */
    public function page(int $page): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->page($page);
    }
}
