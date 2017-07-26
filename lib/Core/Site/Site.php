<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\API\LoadService;
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
        FilterService $filterService,
        FindService $findService,
        LoadService $loadService
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
