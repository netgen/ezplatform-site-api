<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Converter;

use eZ\Publish\API\Repository\Values\ValueObject;
use Netgen\EzPlatformSiteApi\API\Values\Location;

class LocationParamConverter extends SiteParamConverter
{
    protected function getSupportedClass(): string
    {
        return Location::class;
    }

    protected function getPropertyName(): string
    {
        return 'locationId';
    }

    protected function loadValueObject(int $id): ValueObject
    {
        return $this->loadService->loadLocation($id);
    }
}
