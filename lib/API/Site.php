<?php

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Site interface.
 */
interface Site
{
    /**
     * FindService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\FindService
     */
    public function getFindService();

    /**
     * LoadService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\LoadService
     */
    public function getLoadService();
}
