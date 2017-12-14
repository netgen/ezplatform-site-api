<?php

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Site interface.
 */
interface Site
{
    /**
     * Settings getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Settings
     */
    public function getSettings();

    /**
     * FilterService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\FilterService
     */
    public function getFilterService();

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

    /**
     * RelationService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\RelationService
     */
    public function getRelationService();
}
