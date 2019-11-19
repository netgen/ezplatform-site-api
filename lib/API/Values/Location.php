<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;
use Pagerfanta\Pagerfanta;

/**
 * Site Location represents location of Site Content object in the content tree.
 *
 * Corresponds to eZ Platform Repository Location object.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Location
 *
 * @property int|string $id
 * @property int $status
 * @property int $priority
 * @property bool $hidden
 * @property bool $invisible
 * @property string $remoteId
 * @property int|string $parentLocationId
 * @property string $pathString
 * @property int[] $path
 * @property int $depth
 * @property int $sortField
 * @property int $sortOrder
 * @property int|string $contentId
 * @property \eZ\Publish\API\Repository\Values\Content\Location $innerLocation
 * @property \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property null|\Netgen\EzPlatformSiteApi\API\Values\Location $parent
 * @property \Netgen\EzPlatformSiteApi\API\Values\Content $content
 */
abstract class Location extends ValueObject
{
    /**
     * Return an array of children Locations, limited by optional $limit.
     *
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getChildren(int $limit = 25): array;

    /**
     * Return an array of children Locations, filtered by optional
     * $contentTypeIdentifiers, $maxPerPage and $currentPage.
     *
     * @param array $contentTypeIdentifiers
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterChildren(array $contentTypeIdentifiers = [], int $maxPerPage = 25, int $currentPage = 1): Pagerfanta;

    /**
     * Return first child, limited by optional $contentTypeIdentifier.
     *
     * @param null|string $contentTypeIdentifier
     *
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Location
     */
    abstract public function getFirstChild(?string $contentTypeIdentifier = null): ?Location;

    /**
     * Return an array of Location siblings, limited by optional $limit.
     *
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getSiblings(int $limit = 25): array;

    /**
     * Return an array of Location siblings, filtered by optional
     * $contentTypeIdentifiers, $maxPerPage and $currentPage.
     *
     * Siblings will not include current Locations.
     *
     * @param array $contentTypeIdentifiers
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterSiblings(array $contentTypeIdentifiers = [], int $maxPerPage = 25, int $currentPage = 1): Pagerfanta;
}
