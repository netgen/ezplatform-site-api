<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\FilterService as FilterServiceInterface;
use Netgen\EzPlatformSiteApi\API\FindService as FindServiceInterface;
use Netgen\EzPlatformSiteApi\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;

class Site implements SiteInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\FilterService $filterService
     * @param \Netgen\EzPlatformSiteApi\API\FindService $findService
     * @param \Netgen\EzPlatformSiteApi\API\LoadService $loadService
     */
    public function __construct(
        FilterServiceInterface $filterService,
        FindServiceInterface $findService,
        LoadServiceInterface $loadService
    ) {
        $this->filterService = $filterService;
        $this->findService = $findService;
        $this->loadService = $loadService;
    }

    public function getFilterService()
    {
        return $this->filterService;
    }

    public function getFindService()
    {
        return $this->findService;
    }

    public function getLoadService()
    {
        return $this->loadService;
    }
}
