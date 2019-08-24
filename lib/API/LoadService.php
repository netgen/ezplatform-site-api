<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

/**
 * Load service provides methods for loading entities by their ID.
 */
interface LoadService
{
    /**
     * Loads Content object for the given $contentId.
     *
     * @param int|string $contentId
     * @param int $versionNo
     * @param string $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    public function loadContent($contentId, ?int $versionNo = null, ?string $languageCode = null): Content;

    /**
     * Loads Content object for the given $remoteId.
     *
     * @param string $remoteId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    public function loadContentByRemoteId(string $remoteId): Content;

    /**
     * Loads Location object for the given $locationId.
     *
     * @param int|string $locationId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function loadLocation($locationId): Location;

    /**
     * Loads Location object for the given $remoteId.
     *
     * @param string $remoteId
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function loadLocationByRemoteId(string $remoteId): Location;
}
