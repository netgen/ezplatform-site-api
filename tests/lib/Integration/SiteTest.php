<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

/**
 * Test case for the Site.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Site
 *
 * @group site
 *
 * @internal
 */
final class SiteTest extends BaseTest
{
    /**
     * Test for the getSettings() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getSettings()
     */
    public function testGetSettings(): void
    {
        $this->getSite()->getSettings();

        $this->addToAssertionCount(1);
    }

    /**
     * Test for the getFilterService() method.
     *
     * @group filter
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getFilterService()
     */
    public function testGetFilterService(): void
    {
        $this->getSite()->getFilterService();

        $this->addToAssertionCount(1);
    }

    /**
     * Test for the getFindService() method.
     *
     * @group find
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getFindService()
     */
    public function testGetFindService(): void
    {
        $this->getSite()->getFindService();

        $this->addToAssertionCount(1);
    }

    /**
     * Test for the getLoadService() method.
     *
     * @group load
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getLoadService()
     */
    public function testGetLoadService(): void
    {
        $this->getSite()->getLoadService();

        $this->addToAssertionCount(1);
    }
}
