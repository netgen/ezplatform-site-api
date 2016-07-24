<?php

namespace Netgen\EzPlatformSite\Tests\Integration;

/**
 * Test case for the Site.
 *
 * @see \Netgen\EzPlatformSite\API\Site
 *
 * @group site
 */
class SiteTest extends BaseTest
{
    /**
     * Test for the getFindService() method.
     *
     * @see \Netgen\EzPlatformSite\API\Site::getFindService()
     */
    public function testGetFindService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            '\Netgen\EzPlatformSite\API\FindService',
            $site->getFindService()
        );
    }

    /**
     * Test for the getLoadService() method.
     * @see \Netgen\EzPlatformSite\API\Site::getLoadService()
     */
    public function testGetLoadService()
    {
        $site = $this->getSite();
        $this->assertInstanceOf(
            '\Netgen\EzPlatformSite\API\LoadService',
            $site->getLoadService()
        );
    }
}
