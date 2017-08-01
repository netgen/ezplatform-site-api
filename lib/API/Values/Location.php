<?php

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Location represents location of Site Content object in the content tree.
 *
 * Corresponds to eZ Platform Repository Location object.
 * @see \eZ\Publish\API\Repository\Values\Content\Location
 *
 * @property-read int|string $id
 * @property-read int $status
 * @property-read int $priority
 * @property-read bool $hidden
 * @property-read bool $invisible
 * @property-read string $remoteId
 * @property-read int|string $parentLocationId
 * @property-read string $pathString
 * @property-read int[] $path
 * @property-read int $depth
 * @property-read int $sortField
 * @property-read int $sortOrder
 * @property-read int|string $contentId
 * @property-read \eZ\Publish\API\Repository\Values\Content\Location $innerLocation
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Location|null $parent
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Content $content
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
    abstract public function getChildren($limit = 25);

    /**
     * Return an array of children Locations, filtered by optional
     * $contentTypeIdentifiers, $maxPerPage and $currentPage.
     *
     * @param array $contentTypeIdentifiers
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]|\Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterChildren(array $contentTypeIdentifiers = [], $maxPerPage = 25, $currentPage = 1);

    /**
     * Return an array of Location siblings, limited by optional $limit.
     *
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getSiblings($limit = 25);

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
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]|\Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterSiblings(array $contentTypeIdentifiers = [], $maxPerPage = 25, $currentPage = 1);
}
