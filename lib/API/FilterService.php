<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Filters service provides methods for filters entities using
 * eZ Platform Repository Search Query API.
 *
 * In difference to FindService, FilterService always uses synchronous search engine.
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
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function filterContent(Query $query);

    /**
     * Filters Location objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function filterLocations(LocationQuery $query);
}
