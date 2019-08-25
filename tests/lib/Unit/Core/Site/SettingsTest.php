<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;
use Netgen\EzPlatformSiteApi\Core\Site\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Settings value unit tests.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Settings
 *
 * @internal
 */
final class SettingsTest extends TestCase
{
    public function testGetPrioritizedLanguages(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(['cro-HR'], $settings->prioritizedLanguages);
    }

    public function testGetUseAlwaysAvailable(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue($settings->useAlwaysAvailable);
    }

    public function testGetRootLocationId(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(42, $settings->rootLocationId);
    }

    public function testGetFailOnMissingFields(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertFalse($settings->failOnMissingFields);
    }

    public function testGetNonexistentProperty(): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $settings = $this->getSettingsUnderTest();

        $settings->blah;
    }

    public function testIssetPrioritizedLanguages(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->prioritizedLanguages));
    }

    public function testIssetUseAlwaysAvailable(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->useAlwaysAvailable));
    }

    public function testIssetRootLocationId(): void
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->rootLocationId));
    }

    public function testIssetNonexistentProperty(): void
    {
        $this->expectException(PropertyNotFoundException::class);

        $settings = $this->getSettingsUnderTest();

        $this->assertFalse(isset($settings->blah));
    }

    public function testSet(): void
    {
        $this->expectException(PropertyReadOnlyException::class);

        $settings = $this->getSettingsUnderTest();

        $settings->rootLocationId = 24;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Settings
     */
    protected function getSettingsUnderTest(): Settings
    {
        return new Settings(
            ['cro-HR'],
            true,
            42,
            false
        );
    }
}
