<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\FilterService;

/**
 * QueryCollectionMapper maps query configuration from content view to a QueryCollection instance.
 */
final class QueryCollectionMapper
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryTypeMapper
     */
    private $queryTypeMapper;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FilterService
     */
    private $filterService;

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryTypeMapper $queryTypeMapper
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FilterService $filterService
     */
    public function __construct(
        QueryTypeMapper $queryTypeMapper,
        FilterService $filterService
    ) {
        $this->queryTypeMapper = $queryTypeMapper;
        $this->filterService = $filterService;
    }

    /**
     * Map given $view to a QueryCollection instance.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollection
     */
    public function map(ContentView $view)
    {
        $params = [];
        $queryCollection = new QueryCollection(
            $this->queryTypeMapper,
            $this->filterService,
            $view
        );

        if ($view->hasParameter('queries')) {
            $params = $view->getParameter('queries');
        }

        foreach ($params as $key => $options) {
            $queryDefinition = new QueryDefinition(['options' => $options]);
            $queryCollection->addQueryDefinition($key, $queryDefinition);
        }

        return $queryCollection;
    }
}
