<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Pagerfanta adapter for Netgen eZ Platform Site Location filtering.
 * Will return results as Site Location objects.
 */
class LocationSearchFilterAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    private $query;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

    /**
     * @var int
     */
    private $nbResults;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\EzPlatformSiteApi\API\FilterService $filterService
     */
    public function __construct(LocationQuery $query, FilterService $filterService)
    {
        $this->query = $query;
        $this->filterService = $filterService;
    }

    public function getNbResults()
    {
        if (null !== $this->nbResults) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->filterService->filterLocations($countQuery)->totalCount;
    }

    /**
     * Returns a slice of the results, as Site Location objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->filterService->filterLocations($query);

        // Set count for further use if returned by search engine despite !performCount (Solr, ES)
        if (null === $this->nbResults && null !== $searchResult->totalCount) {
            $this->nbResults = $searchResult->totalCount;
        }

        $list = [];
        foreach ($searchResult->searchHits as $hit) {
            $list[] = $hit->valueObject;
        }

        return $list;
    }
}
