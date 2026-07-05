<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WordPressApi\Exceptions\ConnectionException;
use WordPressApi\Exceptions\WordPressApiException;
use WordPressApi\Resources\Posts;
use WordPressApi\Resources\Settings;
use WordPressApi\Support\PaginatedResponse;

/**
 * Regression tests for the transport-level error handling and the
 * pagination/memoization fixes. These mock the HTTP layer via Guzzle's
 * MockHandler — the previous test suite never exercised the network path.
 */
class ErrorHandlingTest extends TestCase
{
    private function httpWith(array $queue): GuzzleClient
    {
        return new GuzzleClient(['handler' => HandlerStack::create(new MockHandler($queue))]);
    }

    private function posts(GuzzleClient $http): Posts
    {
        return new Posts($http, 'https://example.com/wp-json', '/wp/v2/posts');
    }

    public function testConnectFailureThrowsConnectionException(): void
    {
        $http = $this->httpWith([
            new ConnectException('Connection refused', new Request('GET', 'test')),
        ]);

        $this->expectException(ConnectionException::class);
        $this->posts($http)->query([]);
    }

    public function testServerErrorThrowsWordPressApiExceptionButNotConnectionException(): void
    {
        $http = $this->httpWith([new Response(500, [], 'Server Error')]);

        try {
            $this->posts($http)->query([]);
            $this->fail('Expected WordPressApiException on HTTP 500');
        } catch (WordPressApiException $e) {
            // A 5xx is a server error, not a connection failure.
            $this->assertNotInstanceOf(ConnectionException::class, $e);
        }
    }

    public function testConnectFailureOnGetByIdThrowsConnectionException(): void
    {
        $http = $this->httpWith([
            new ConnectException('Connection refused', new Request('GET', 'test')),
        ]);

        $this->expectException(ConnectionException::class);
        $this->posts($http)->get(1);
    }

    public function testNextAndPreviousPageReturnNullAtBoundaries(): void
    {
        $http = $this->httpWith([]); // no request should be made at the boundaries
        $response = new Response(200, ['X-WP-Total' => '3', 'X-WP-TotalPages' => '1'], '[]');
        $paginated = new PaginatedResponse($response, [], $http, 'https://example.com/wp-json/wp/v2/posts', ['page' => 1]);

        $this->assertNull($paginated->nextPage(), 'nextPage() must return null on the last page');
        $this->assertNull($paginated->previousPage(), 'previousPage() must return null on the first page');
    }

    public function testSettingsGettersMemoizeAndFireSingleRequest(): void
    {
        // Only ONE response is queued: if any getter fired a second HTTP
        // request, the empty mock queue would throw and fail the test.
        $http = $this->httpWith([
            new Response(200, [], (string) json_encode([
                'title' => 'Example',
                'url' => 'https://example.com',
                'email' => 'admin@example.com',
            ])),
        ]);
        $settings = new Settings($http, 'https://example.com/wp-json');

        $this->assertSame('Example', $settings->getTitle());
        $this->assertSame('https://example.com', $settings->getUrl());
        $this->assertSame('admin@example.com', $settings->getEmail());
    }
}
