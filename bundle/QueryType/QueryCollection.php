<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use ArrayAccess;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSiteApi\Core\Site\FilterService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter;
use Pagerfanta\Pagerfanta;
use RuntimeException;

/**
 * QueryCollection contains a map of QueryDefinitions by their key string,
 * implemented as ArrayAccess. QueryDefinitions are mapped to a Query object and
 * executed in a lazy way, as accessed.
 */
final class QueryCollection implements ArrayAccess
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition[]
     */
    private $queryDefinitionMap = [];

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchResult[]
     */
    private $resultMap = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FilterService
     */
    private $filterService;

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FilterService $filterService
     */
    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        FilterService $filterService
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->filterService = $filterService;
    }

    public function addQueryDefinition($key, QueryDefinition $queryDefinition)
    {
        $this->queryDefinitionMap[$key] = $queryDefinition;
    }

    public function offsetExists($offset)
    {
        return isset($this->queryDefinitionMap[$offset]);
    }

    /**
     * @inheritdoc
     *
     * @throws \Pagerfanta\Exception\Exception
     * @throws \RuntimeException
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists($offset, $this->resultMap)) {
            $this->resultMap[$offset] = $this->executeQuery($offset);
        }

        return $this->resultMap[$offset];
    }

    public function offsetSet($offset, $value)
    {
        // Do nothing
    }

    public function offsetUnset($offset)
    {
        // Do nothing
    }

    /**
     * Execute search query by the QueryDefinition at the given $offset.
     *
     * @throws \Pagerfanta\Exception\Exception
     * @throws \RuntimeException
     *
     * @param string $name
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|Pagerfanta
     */
    private function executeQuery($name)
    {
        if (!array_key_exists($name, $this->queryDefinitionMap)) {
            throw new RuntimeException("Found no QueryDefinition for key '{$name}'");
        }

        $queryDefinition = $this->queryDefinitionMap[$name];
        $queryType = $this->queryTypeRegistry->getQueryType($queryDefinition->name);
        $query = $queryType->getQuery($queryDefinition->parameters);

        if ($query instanceof LocationQuery) {
            return $this->getLocationResult($query, $queryDefinition);
        }

        if ($query instanceof Query) {
            return $this->getContentResult($query, $queryDefinition);
        }

        throw new RuntimeException('Could not handle given query');
    }

    /**
     * Return search result by the given parameters.
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    private function getLocationResult(LocationQuery $query, QueryDefinition $queryDefinition)
    {
        $usePager = $this->getUsePager($queryDefinition);

        if ($usePager) {
            $pager = new Pagerfanta(
                new LocationSearchFilterAdapter($query, $this->filterService)
            );

            $pager->setNormalizeOutOfRangePages(true);
            $pager->setMaxPerPage($this->getMaxPerPage($queryDefinition));
            $pager->setCurrentPage($this->getCurrentPage($queryDefinition));

            return $pager;
        }

        return $this->filterService->filterLocations($query);
    }

    /**
     * Return search result by the given parameters.
     *
     * @throws \Pagerfanta\Exception\Exception
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
     */
    private function getContentResult(Query $query, QueryDefinition $queryDefinition)
    {
        $usePager = $this->getUsePager($queryDefinition);

        if ($usePager) {
            $pager = new Pagerfanta(
                new ContentSearchFilterAdapter($query, $this->filterService)
            );

            $pager->setNormalizeOutOfRangePages(true);
            $pager->setMaxPerPage($this->getMaxPerPage($queryDefinition));
            $pager->setCurrentPage($this->getCurrentPage($queryDefinition));

            return $pager;
        }

        return $this->filterService->filterContent($query);
    }

    private function getUsePager(QueryDefinition $queryDefinition)
    {
        return !array_key_exists('use_pager', $queryDefinition->parameters) || true === $queryDefinition->parameters['pager'];
    }

    private function getMaxPerPage(QueryDefinition $queryDefinition)
    {
        if (array_key_exists('max_per_page', $queryDefinition->parameters)) {
            return $queryDefinition->parameters['max_per_page'];
        }

        return 25;
    }

    private function getCurrentPage(QueryDefinition $queryDefinition)
    {
        if (array_key_exists('current_page', $queryDefinition->parameters)) {
            return $queryDefinition->parameters['current_page'];
        }

        return 1;
    }
}
