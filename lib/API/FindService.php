<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Find service provides methods for finding entities using eZ Platform Repository Search Query API.
 *
 * Unlike FilterService, FindService uses search engine configured for the repository (Legacy or Solr).
 */
interface FindService
{
    /**
     * Finds Content objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function findContent(Query $query): SearchResult;

    /**
     * Finds Location objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function findLocations(LocationQuery $query): SearchResult;
}
