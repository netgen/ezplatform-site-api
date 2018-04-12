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
     * @var \eZ\Publish\API\Repository\Values\Content\Search\Facet[]
     */
    private $facets;

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

        return $this->nbResults = $this->getSearchResult()->totalCount;
    }

    /**
     * Returns the facets of the results.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\Facet[] The facets of the results
     */
    public function getFacets()
    {
        if (isset($this->facets)) {
            return $this->facets;
        }

        return $this->facets = $this->getSearchResult()->facets;
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

        if (!isset($this->facets) && isset($this->searchResult->facets)) {
            $this->facets = $this->searchResult->facets;
        }

        return $this->searchResult->searchHits;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private function getSearchResult()
    {
        if ($this->searchResult === null) {
            $query = clone $this->query;
            $query->limit = 0;
            $this->searchResult = $this->findService->findLocations($query);
        }

        return $this->searchResult;
    }
}
