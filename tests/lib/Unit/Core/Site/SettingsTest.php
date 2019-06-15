<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException;
use Netgen\EzPlatformSiteApi\Core\Site\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Settings value unit tests.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Settings
 */
class SettingsTest extends TestCase
{
    public function testGetPrioritizedLanguages()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(['cro-HR'], $settings->prioritizedLanguages);
    }

    public function testGetUseAlwaysAvailable()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(true, $settings->useAlwaysAvailable);
    }

    public function testGetRootLocationId()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(42, $settings->rootLocationId);
    }

    public function testGetFailOnMissingFields()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertEquals(false, $settings->failOnMissingFields);
    }

    public function testGetNonexistentProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $settings = $this->getSettingsUnderTest();

        $settings->blah;
    }

    public function testIssetPrioritizedLanguages()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->prioritizedLanguages));
    }

    public function testIssetUseAlwaysAvailable()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->useAlwaysAvailable));
    }

    public function testIssetRootLocationId()
    {
        $settings = $this->getSettingsUnderTest();

        $this->assertTrue(isset($settings->rootLocationId));
    }

    public function testIssetNonexistentProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $settings = $this->getSettingsUnderTest();

        $this->assertFalse(isset($settings->blah));
    }

    public function testSet()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $settings = $this->getSettingsUnderTest();

        $settings->rootLocationId = 24;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Settings
     */
    protected function getSettingsUnderTest()
    {
        return new Settings(
            ['cro-HR'],
            true,
            42,
            false
        );
    }
}
