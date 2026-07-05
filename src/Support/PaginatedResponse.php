<?php

declare(strict_types=1);

namespace WordPressApi\Support;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;

class PaginatedResponse
{
    /** @var array<int, mixed> */
    private array $data;

    private int $total;
    private int $totalPages;
    private int $currentPage;

    /** @var array<string, mixed> */
    private array $params;

    private GuzzleClient $client;
    private string $endpoint;

    /**
     * @param array<int, mixed> $data
     * @param array<string, mixed> $params
     */
    public function __construct(
        ResponseInterface $response,
        array $data,
        GuzzleClient $client,
        string $endpoint,
        array $params = []
    ) {
        $this->data = $data;
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->params = $params;

        // Parse WordPress pagination headers
        $this->total = (int) ($response->getHeader('X-WP-Total')[0] ?? 0);
        $this->totalPages = (int) ($response->getHeader('X-WP-TotalPages')[0] ?? 1);
        $this->currentPage = (int) ($params['page'] ?? 1);
    }

    /**
     * Get the data for current page
     *
     * @return array<int, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get total number of items
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get total number of pages
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Get current page number
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Check if there is a next page
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    /**
     * Check if there is a previous page
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Fetch the next page
     */
    public function nextPage(): self
    {
        if (!$this->hasNextPage()) {
            return $this;
        }

        $params = array_merge($this->params, ['page' => $this->currentPage + 1]);
        $response = $this->client->get($this->endpoint, ['query' => $params]);
        $data = json_decode((string) $response->getBody(), true);

        return new self($response, $data, $this->client, $this->endpoint, $params);
    }

    /**
     * Fetch the previous page
     */
    public function previousPage(): self
    {
        if (!$this->hasPreviousPage()) {
            return $this;
        }

        $params = array_merge($this->params, ['page' => $this->currentPage - 1]);
        $response = $this->client->get($this->endpoint, ['query' => $params]);
        $data = json_decode((string) $response->getBody(), true);

        return new self($response, $data, $this->client, $this->endpoint, $params);
    }
}
