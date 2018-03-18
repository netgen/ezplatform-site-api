<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use ArrayAccess;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\FilterService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter;
use Pagerfanta\Pagerfanta;
use RuntimeException;

/**
 * todo
 */
final class QueryCollection implements ArrayAccess
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition[]
     */
    private $queryDefinitionMap = [];

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchResult[]
     */
    private $resultMap = [];

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryTypeMapper
     */
    private $queryTypeMapper;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    private $view;

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryTypeMapper $queryTypeMapper
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FilterService $filterService
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     */
    public function __construct(
        QueryTypeMapper $queryTypeMapper,
        FilterService $filterService,
        ContentView $view
    ) {
        $this->queryTypeMapper = $queryTypeMapper;
        $this->filterService = $filterService;
        $this->view = $view;
    }

    public function addQueryDefinition($key, QueryDefinition $queryDefinition)
    {
        $this->queryDefinitionMap[$key] = $queryDefinition;
    }

    public function offsetExists($offset)
    {
        return isset($this->queryDefinitionMap[$offset]);
    }

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
     * @param string $offset
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|Pagerfanta
     */
    private function executeQuery($offset)
    {
        $queryDefinition = $this->queryDefinitionMap[$offset];
        $query = $this->queryTypeMapper->map($this->view, $queryDefinition);

        if ($query instanceof LocationQuery) {
            return $this->getLocationResult($query, $queryDefinition);
        }

        if ($query instanceof Query) {
            return $this->getContentResult($query, $queryDefinition);
        }

        throw new RuntimeException("Could not handle given query");
    }

    /**
     * Return search result by the given parameters.
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
        $options = $queryDefinition->options;

        return !array_key_exists('use_pager', $options) || true === $options['pager'];
    }

    private function getMaxPerPage(QueryDefinition $queryDefinition)
    {
        if (array_key_exists('max_per_page', $queryDefinition->options)) {
            return $queryDefinition->options['max_per_page'];
        }

        return 25;
    }

    private function getCurrentPage(QueryDefinition $queryDefinition)
    {
        if (array_key_exists('current_page', $queryDefinition->options)) {
            return $queryDefinition->options['current_page'];
        }

        return 1;
    }
}
