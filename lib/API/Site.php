<?php

declare(strict_types=1);

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
    public function getSettings(): Settings;

    /**
     * FilterService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\FilterService
     */
    public function getFilterService(): FilterService;

    /**
     * FindService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\FindService
     */
    public function getFindService(): FindService;

    /**
     * LoadService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\LoadService
     */
    public function getLoadService(): LoadService;

    /**
     * RelationService getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\RelationService
     */
    public function getRelationService(): RelationService;
}
