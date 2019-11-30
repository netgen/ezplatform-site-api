<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Converter;

use Netgen\EzPlatformSiteApi\API\Values\Location;

final class LocationParamConverter extends SiteParamConverter
{
    protected function getSupportedClass(): string
    {
        return Location::class;
    }

    protected function getPropertyName(): string
    {
        return 'locationId';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    protected function loadValueObject(int $id): Location
    {
        return $this->loadService->loadLocation($id);
    }
}
