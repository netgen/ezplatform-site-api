<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @deprecated since version 2.5, to be removed in 3.0. Use FindAdapter or FilterAdapter instead.
 *
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
     * @var \eZ\Publish\API\Repository\Values\Content\Search\Facet[]
     */
    private $facets;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private $searchResult;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\EzPlatformSiteApi\API\FilterService $filterService
     */
    public function __construct(LocationQuery $query, FilterService $filterService)
    {
        @trigger_error(
            'LocationSearchFilterAdapter is deprecated since version 2.5 and will be removed in 3.0. Use FindAdapter or FilterAdapter instead.',
            E_USER_DEPRECATED
        );

        $this->query = $query;
        $this->filterService = $filterService;
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

        $this->searchResult = $this->filterService->filterLocations($query);

        // Set count for further use if returned by search engine despite !performCount (Solr, ES)
        if (!isset($this->nbResults) && isset($this->searchResult->totalCount)) {
            $this->nbResults = $this->searchResult->totalCount;
        }

        if (!isset($this->facets) && isset($this->searchResult->facets)) {
            $this->facets = $this->searchResult->facets;
        }

        $list = [];
        foreach ($this->searchResult->searchHits as $hit) {
            $list[] = $hit->valueObject;
        }

        return $list;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private function getSearchResult()
    {
        if ($this->searchResult === null) {
            $query = clone $this->query;
            $query->limit = 0;
            $this->searchResult = $this->filterService->filterLocations($query);
        }

        return $this->searchResult;
    }
}
