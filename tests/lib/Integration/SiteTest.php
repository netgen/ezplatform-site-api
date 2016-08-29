<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

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
     * Test for the getFindService() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Site::getFindService()
     */
    public function testGetFindService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            '\Netgen\EzPlatformSiteApi\API\FindService',
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
            '\Netgen\EzPlatformSiteApi\API\LoadService',
            $site->getLoadService()
        );
    }
}
