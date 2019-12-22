<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationResolver;

use Exception;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationResolver;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

class SudoMainLocationResolver extends LocationResolver
{
    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    public function __construct(Repository $repository, LoadService $loadService)
    {
        $this->repository = $repository;
        $this->loadService = $loadService;
    }

    public function getLocation(Content $content): Location
    {
        if ($content->mainLocationId === null) {
            throw new NotFoundException('main Location of Content', $content->id);
        }

        try {
            return $this->repository->sudo(
                function (Repository $repository) use ($content): Location {
                    return $this->loadService->loadLocation($content->mainLocationId);
                }
            );
        } catch (Exception $e) {
            throw new NotFoundException('main Location of Content', $content->id);
        }
    }
}
