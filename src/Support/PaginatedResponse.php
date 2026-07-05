<?php

declare(strict_types=1);

namespace WordPressApi\Support;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use WordPressApi\Exceptions\ConnectionException;
use WordPressApi\Exceptions\WordPressApiException;

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
     * Fetch the next page, or null if this is the last page.
     *
     * @throws ConnectionException
     * @throws WordPressApiException
     */
    public function nextPage(): ?self
    {
        if (!$this->hasNextPage()) {
            return null;
        }

        return $this->fetchPage($this->currentPage + 1);
    }

    /**
     * Fetch the previous page, or null if this is the first page.
     *
     * @throws ConnectionException
     * @throws WordPressApiException
     */
    public function previousPage(): ?self
    {
        if (!$this->hasPreviousPage()) {
            return null;
        }

        return $this->fetchPage($this->currentPage - 1);
    }

    /**
     * Fetch a specific page, mapping transport errors to SDK exceptions.
     *
     * @throws ConnectionException
     * @throws WordPressApiException
     */
    private function fetchPage(int $page): self
    {
        $params = array_merge($this->params, ['page' => $page]);

        try {
            $response = $this->client->get($this->endpoint, ['query' => $params]);
        } catch (ConnectException $e) {
            throw new ConnectionException("Could not connect to WordPress API: " . $e->getMessage(), 0, $e);
        } catch (GuzzleException $e) {
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }

        $data = json_decode((string) $response->getBody(), true);
        if (!is_array($data)) {
            $data = [];
        }

        return new self($response, $data, $this->client, $this->endpoint, $params);
    }
}
