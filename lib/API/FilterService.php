<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Filters service provides methods for filters entities using
 * eZ Platform Repository Search Query API.
 *
 * Unlike FindService, FilterService always uses Legacy search engine.
 */
interface FilterService
{
    /**
     * Filters Content objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Content
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function filterContent(Query $query): SearchResult;

    /**
     * Filters Location objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function filterLocations(LocationQuery $query): SearchResult;
}
