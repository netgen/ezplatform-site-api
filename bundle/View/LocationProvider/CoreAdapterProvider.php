<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationProvider;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Helper\ContentInfoLocationLoader;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationProvider;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

class CoreAdapterProvider extends LocationProvider
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    /**
     * @var \eZ\Publish\Core\Helper\ContentInfoLocationLoader
     */
    private $coreLoader;

    public function __construct(
        Repository $repository,
        LoadService $loadService,
        ContentInfoLocationLoader $coreLoader
    ) {
        $this->repository = $repository;
        $this->loadService = $loadService;
        $this->coreLoader = $coreLoader;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getLocation(Content $content): Location
    {
        $repoLocation = $this->coreLoader->loadLocation($content->contentInfo->innerContentInfo);

        return $this->repository->sudo(
            function (Repository $repository) use ($repoLocation): Location {
                return $this->loadService->loadLocation($repoLocation->id);
            }
        );
    }
}
