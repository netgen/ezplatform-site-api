<?php

namespace Netgen\EzPlatformSite\Core\Site;

use Netgen\EzPlatformSite\API\FindService as FindServiceInterface;
use Netgen\EzPlatformSite\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSite\API\Site as SiteInterface;

final class Site implements SiteInterface
{
    /**
     * @var \Netgen\EzPlatformSite\API\FindService
     */
    private $findService;

    /**
     * @var \Netgen\EzPlatformSite\API\LoadService
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
