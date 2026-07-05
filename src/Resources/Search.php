<?php

declare(strict_types=1);

namespace WordPressApi\Resources;

use WordPressApi\Support\QueryBuilder;

class Search extends AbstractResource
{
    /**
     * Filter by content type
     */
    public function type(string $type): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->where('type', $type);
    }

    /**
     * Filter by subtype
     */
    public function subtype(string $subtype): QueryBuilder
    {
        return (new QueryBuilder($this->client, $this->endpoint))->where('subtype', $subtype);
    }
}
