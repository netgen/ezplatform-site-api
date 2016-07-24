<?php

namespace Netgen\EzPlatformSite\API;

/**
 * Site interface.
 */
interface Site
{
    /**
     * FindService getter.
     *
     * @return \Netgen\EzPlatformSite\API\FindService
     */
    public function getFindService();

    /**
     * LoadService getter.
     *
     * @return \Netgen\EzPlatformSite\API\LoadService
     */
    public function getLoadService();
}
