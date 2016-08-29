<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\FindService as FindServiceInterface;
use Netgen\EzPlatformSiteApi\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;

final class Site implements SiteInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    public function __construct(
        FindServiceInterface $findService,
        LoadServiceInterface $loadService
    ) {
        $this->findService = $findService;
        $this->loadService = $loadService;
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
