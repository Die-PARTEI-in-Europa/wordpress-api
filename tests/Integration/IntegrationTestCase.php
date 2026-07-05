<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Integration;

use PHPUnit\Framework\TestCase;
use WordPressApi\Client;

abstract class IntegrationTestCase extends TestCase
{
    protected Client $client;
    protected string $baseUrl = 'http://localhost:8080';

    protected function setUp(): void
    {
        parent::setUp();

        // Check if WordPress is running
        if (!$this->isWordPressRunning()) {
            $this->markTestSkipped(
                'WordPress is not running. Start it with: docker-compose up -d'
            );
        }

        $this->client = new Client($this->baseUrl);
    }

    /**
     * Check if WordPress is accessible
     */
    protected function isWordPressRunning(): bool
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'ignore_errors' => true,
            ],
        ]);

        $result = @file_get_contents($this->baseUrl . '/wp-json', false, $context);
        return $result !== false;
    }

    /**
     * Wait for WordPress to be ready
     */
    protected function waitForWordPress(int $maxWaitSeconds = 60): void
    {
        $start = time();
        while (time() - $start < $maxWaitSeconds) {
            if ($this->isWordPressRunning()) {
                return;
            }
            sleep(2);
        }
        $this->fail('WordPress did not become ready in time');
    }
}
