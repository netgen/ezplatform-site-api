<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSearchExtra\Core\Pagination\Pagerfanta\BaseAdapter;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FindAdapter;
use Pagerfanta\Pagerfanta;

/**
 * QueryExecutor resolves the Query from the QueryDefinition, executes it and returns the result.
 *
 * @internal do not depend on this service, it can be changed without warning
 */
final class QueryExecutor
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        FilterService $filterService,
        FindService $findService
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->filterService = $filterService;
        $this->findService = $findService;
    }

    /**
     * Execute the Query with the given $name and return the result.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function execute(QueryDefinition $queryDefinition): Pagerfanta
    {
        $adapter = $this->getPagerAdapter($queryDefinition);
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($queryDefinition->maxPerPage);
        $pager->setCurrentPage($queryDefinition->page);

        return $pager;
    }

    /**
     * Execute the Query with the given $name and return the result.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function executeRaw(QueryDefinition $queryDefinition): SearchResult
    {
        $query = $this->getQuery($queryDefinition);

        if ($query instanceof LocationQuery) {
            return $this->getLocationResult($query, $queryDefinition);
        }

        return $this->getContentResult($query, $queryDefinition);
    }

    private function getPagerAdapter(QueryDefinition $queryDefinition): BaseAdapter
    {
        $query = $this->getQuery($queryDefinition);

        if ($queryDefinition->useFilter) {
            return new FilterAdapter($query, $this->filterService);
        }

        return new FindAdapter($query, $this->findService);
    }

    /**
     * Return search result by the given parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private function getLocationResult(LocationQuery $query, QueryDefinition $queryDefinition): SearchResult
    {
        if ($queryDefinition->useFilter) {
            return $this->filterService->filterLocations($query);
        }

        return $this->findService->findLocations($query);
    }

    /**
     * Return search result by the given parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private function getContentResult(Query $query, QueryDefinition $queryDefinition): SearchResult
    {
        if ($queryDefinition->useFilter) {
            return $this->filterService->filterContent($query);
        }

        return $this->findService->findContent($query);
    }

    private function getQuery(QueryDefinition $queryDefinition): Query
    {
        $queryType = $this->queryTypeRegistry->getQueryType($queryDefinition->name);

        return $queryType->getQuery($queryDefinition->parameters);
    }
}
