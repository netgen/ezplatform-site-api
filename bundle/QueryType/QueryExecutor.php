<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FindAdapter;
use Pagerfanta\Pagerfanta;
use RuntimeException;

/**
 * QueryExecutor resolves the Query from the QueryDefinition, executes it and returns the result.
 *
 * @internal Do not depend on this service, it can be changed without warning.
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

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\EzPlatformSiteApi\API\FilterService $filterService
     * @param \Netgen\EzPlatformSiteApi\API\FindService $findService
     */
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
     * @throws \Pagerfanta\Exception\Exception
     * @throws \RuntimeException
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     * @param bool $usePager
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    public function execute(QueryDefinition $queryDefinition, $usePager)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($queryDefinition->name);
        $query = $queryType->getQuery($queryDefinition->parameters);

        if ($usePager) {
            return $this->getPager($query, $queryDefinition);
        }

        if ($query instanceof LocationQuery) {
            return $this->getLocationResult($query, $queryDefinition);
        }

        if ($query instanceof Query) {
            return $this->getContentResult($query, $queryDefinition);
        }

        throw new RuntimeException('Could not handle given query');
    }

    /**
     * Return Pagerfanta instance by the given parameters.
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @return \Pagerfanta\Pagerfanta
     */
    private function getPager(Query $query, QueryDefinition $queryDefinition): Pagerfanta
    {
        if ($queryDefinition->useFilter) {
            $adapter = new FilterAdapter($query, $this->filterService);
        } else {
            $adapter = new FindAdapter($query, $this->findService);
        }

        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($queryDefinition->maxPerPage);
        $pager->setCurrentPage($queryDefinition->page);

        return $pager;
    }

    /**
     * Return search result by the given parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
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
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    private function getContentResult(Query $query, QueryDefinition $queryDefinition): SearchResult
    {
        if ($queryDefinition->useFilter) {
            return $this->filterService->filterContent($query);
        }

        return $this->findService->findContent($query);
    }
}
