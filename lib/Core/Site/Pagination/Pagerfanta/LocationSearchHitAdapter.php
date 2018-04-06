<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\FindService;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Pagerfanta adapter for Netgen eZ Platform Site Location search.
 * Will return results as SearchHit objects.
 */
class LocationSearchHitAdapter implements AdapterInterface
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    private $query;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var int
     */
    private $nbResults;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private $searchResult;

    public function __construct(LocationQuery $query, FindService $findService)
    {
        $this->query = $query;
        $this->findService = $findService;
    }

    /**
     * Returns the number of results.
     *
     * @return int The number of results
     */
    public function getNbResults()
    {
        if (isset($this->nbResults)) {
            return $this->nbResults;
        }

        $countQuery = clone $this->query;
        $countQuery->limit = 0;

        return $this->nbResults = $this->findService->findLocations($countQuery)->totalCount;
    }

    /**
     * Returns a slice of the results, as SearchHit objects.
     *
     * @param int $offset The offset
     * @param int $length The length
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[]
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $this->searchResult = $this->findService->findLocations($query);

        // Set count for further use if returned by search engine despite !performCount (Solr, ES)
        if (!isset($this->nbResults) && isset($this->searchResult->totalCount)) {
            $this->nbResults = $this->searchResult->totalCount;
        }

        return $this->searchResult->searchHits;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function getSearchResult()
    {
        if ($this->searchResult === null) {
            $this->searchResult = $this->findService->findLocations($this->query);
        }

        return $this->searchResult;
    }
}
