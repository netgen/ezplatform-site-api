<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\API\LoadService;

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
     * Test for the getFilterService() method.
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
