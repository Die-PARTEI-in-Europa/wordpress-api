<?php

declare(strict_types=1);

namespace WordPressApi\Contracts;

use WordPressApi\Support\PaginatedResponse;

interface ResourceInterface
{
    /**
     * Fetch all items with optional parameters
     *
     * @param array<string, mixed> $params
     */
    public function all(array $params = []): PaginatedResponse;

    /**
     * Fetch a single item by ID
     */
    public function get(int $id): array;

    /**
     * Custom query with parameters
     *
     * @param array<string, mixed> $params
     */
    public function query(array $params): PaginatedResponse;
}
