<?php

namespace Netgen\EzPlatformSiteApi\API;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Find service provides methods for finding entities using eZ Platform Repository Search Query API.
 */
interface FindService
{
    /**
     * Finds Content objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Content
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findContent(Query $query);

    /**
     * @deprecated since version 2.2, to be removed in 3.0. Use findContent() instead.
     *
     * Finds ContentInfo objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findContentInfo(Query $query);

    /**
     * Finds Location objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Location
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findLocations(LocationQuery $query);

    /**
     * @deprecated since version 2.1, to be removed in 3.0. Use findLocations() instead.
     *
     * Finds Node objects for the given $query.
     *
     * @see \Netgen\EzPlatformSiteApi\API\Values\Node
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function findNodes(LocationQuery $query);
}
