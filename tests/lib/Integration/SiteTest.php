<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Settings;

/**
 * Test case for the Site.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Site
 *
 * @group site
 */
class SiteTest extends BaseTest
{
    /**
     * Test for the getSettings() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getSettings()
     */
    public function testGetSettings()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            Settings::class,
            $site->getSettings()
        );
    }

    /**
     * Test for the getFilterService() method.
     *
     * @group filter
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getFilterService()
     */
    public function testGetFilterService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            FilterService::class,
            $site->getFilterService()
        );
    }

    /**
     * Test for the getFindService() method.
     *
     * @group find
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getFindService()
     */
    public function testGetFindService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            FindService::class,
            $site->getFindService()
        );
    }

    /**
     * Test for the getLoadService() method.
     *
     * @group load
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getLoadService()
     */
    public function testGetLoadService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            LoadService::class,
            $site->getLoadService()
        );
    }
}
