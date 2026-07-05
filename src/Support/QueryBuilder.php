<?php

declare(strict_types=1);

namespace WordPressApi\Support;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use WordPressApi\Exceptions\ConnectionException;
use WordPressApi\Exceptions\WordPressApiException;

class QueryBuilder
{
    /** @var array<string, mixed> */
    private array $params = [];

    private GuzzleClient $client;
    private string $endpoint;

    public function __construct(GuzzleClient $client, string $endpoint)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
    }

    /**
     * Add a where clause
     *
     * @param mixed $value
     */
    public function where(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Filter by language (WPML support)
     */
    public function language(string $languageCode): self
    {
        $this->params['lang'] = $languageCode;
        return $this;
    }

    /**
     * Set order by field
     */
    public function orderBy(string $field, string $direction = 'asc'): self
    {
        $this->params['orderby'] = $field;
        $this->params['order'] = $direction;
        return $this;
    }

    /**
     * Set results per page
     */
    public function perPage(int $perPage): self
    {
        $this->params['per_page'] = $perPage;
        return $this;
    }

    /**
     * Set page number
     */
    public function page(int $page): self
    {
        $this->params['page'] = $page;
        return $this;
    }

    /**
     * Search for a term
     */
    public function search(string $term): self
    {
        $this->params['search'] = $term;
        return $this;
    }

    /**
     * Filter by content type (for search)
     */
    public function type(string $type): self
    {
        $this->params['type'] = $type;
        return $this;
    }

    /**
     * Filter by subtype (for search)
     */
    public function subtype(string $subtype): self
    {
        $this->params['subtype'] = $subtype;
        return $this;
    }

    /**
     * Execute the query and return paginated response
     *
     * @throws ConnectionException
     * @throws WordPressApiException
     */
    public function get(): PaginatedResponse
    {
        try {
            $response = $this->client->get($this->endpoint, ['query' => $this->params]);
        } catch (ConnectException $e) {
            throw new ConnectionException("Could not connect to WordPress API: " . $e->getMessage(), 0, $e);
        } catch (GuzzleException $e) {
            throw new WordPressApiException("API request failed: " . $e->getMessage(), $e->getCode(), $e);
        }

        $data = json_decode((string) $response->getBody(), true);

        // Handle empty or invalid JSON response
        if (!is_array($data)) {
            $data = [];
        }

        return new PaginatedResponse(
            $response,
            $data,
            $this->client,
            $this->endpoint,
            $this->params
        );
    }

    /**
     * Get the query parameters
     *
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
