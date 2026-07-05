<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WordPressApi\Client;
use WordPressApi\Resources\Posts;
use WordPressApi\Resources\Pages;
use WordPressApi\Resources\Menus;
use WordPressApi\Resources\Search;

class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client('https://example.com');
    }

    public function testClientInitialization(): void
    {
        $this->assertInstanceOf(Client::class, $this->client);
    }

    public function testPostsResource(): void
    {
        $posts = $this->client->posts();
        $this->assertInstanceOf(Posts::class, $posts);
    }

    public function testPagesResource(): void
    {
        $pages = $this->client->pages();
        $this->assertInstanceOf(Pages::class, $pages);
    }

    public function testMenusResource(): void
    {
        $menus = $this->client->menus();
        $this->assertInstanceOf(Menus::class, $menus);
    }

    public function testSearchResource(): void
    {
        $search = $this->client->search();
        $this->assertInstanceOf(Search::class, $search);
    }

    public function testBaseUrlIsCorrect(): void
    {
        $this->assertEquals('https://example.com/wp-json', $this->client->getBaseUrl());
    }

    public function testBaseUrlWithTrailingSlash(): void
    {
        $client = new Client('https://example.com/');
        $this->assertEquals('https://example.com/wp-json', $client->getBaseUrl());
    }

    public function testClientWithAuthentication(): void
    {
        // In production, use Application Passwords (format: 'xxxx xxxx xxxx xxxx xxxx xxxx')
        $client = new Client('https://example.com', [
            'auth' => ['username', 'xxxx xxxx xxxx xxxx xxxx xxxx']
        ]);

        $this->assertInstanceOf(Client::class, $client);
    }
}
