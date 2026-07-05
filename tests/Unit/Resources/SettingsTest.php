<?php

declare(strict_types=1);

namespace WordPressApi\Tests\Unit\Resources;

use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use WordPressApi\Resources\Settings;

class SettingsTest extends TestCase
{
    private Settings $settings;
    private GuzzleClient $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleClient();
        $this->settings = new Settings($this->client, 'https://example.com/wp-json');
    }

    public function testSettingsInstantiation(): void
    {
        $this->assertInstanceOf(Settings::class, $this->settings);
    }

    public function testGetTitleMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getTitle'));
    }

    public function testGetDescriptionMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getDescription'));
    }

    public function testGetUrlMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getUrl'));
    }

    public function testGetLanguageMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getLanguage'));
    }

    public function testGetTimezoneMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getTimezone'));
    }

    public function testGetDateFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getDateFormat'));
    }

    public function testGetTimeFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getTimeFormat'));
    }

    public function testGetShowOnFrontMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getShowOnFront'));
    }

    public function testGetFrontPageIdMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getFrontPageId'));
    }

    public function testGetPostsPageIdMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getPostsPageId'));
    }

    public function testGetPostsPerPageMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'getPostsPerPage'));
    }

    public function testAllMethodExists(): void
    {
        $this->assertTrue(method_exists($this->settings, 'all'));
    }
}
