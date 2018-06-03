<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\SearchResultExtras;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * Base Site API search adapter.
 */
abstract class BaseAdapter implements AdapterInterface, SearchResultExtras
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Query
     */
    private $query;

    /**
     * @var int
     */
    private $nbResults;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\Facet[]
     */
    private $facets;

    /**
     * @var float
     */
    private $maxScore;

    /**
     * @var int
     */
    private $time;

    /**
     * @var bool
     */
    private $isExtraInfoInitialized = false;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function getNbResults()
    {
        $this->initializeExtraInfo();

        return $this->nbResults;
    }

    public function getFacets()
    {
        $this->initializeExtraInfo();

        return $this->facets;
    }

    public function getMaxScore()
    {
        $this->initializeExtraInfo();

        return $this->maxScore;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        $query->offset = $offset;
        $query->limit = $length;
        $query->performCount = false;

        $searchResult = $this->executeQuery($query);

        $this->time = $searchResult->time;

        if (!$this->isExtraInfoInitialized && $searchResult->totalCount !== null) {
            $this->setExtraInfo($searchResult);
        }

        return new Slice($searchResult->searchHits);
    }

    /**
     * Execute the given $query and return SearchResult instance.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    abstract protected function executeQuery(Query $query);

    private function initializeExtraInfo()
    {
        if ($this->isExtraInfoInitialized) {
            return;
        }

        $query = clone $this->query;
        $query->limit = 0;
        $searchResult = $this->executeQuery($query);

        $this->setExtraInfo($searchResult);
    }

    private function setExtraInfo(SearchResult $searchResult)
    {
        $this->facets = $searchResult->facets;
        $this->maxScore = $searchResult->maxScore;
        $this->nbResults = $searchResult->totalCount;

        $this->isExtraInfoInitialized = true;
    }
}
