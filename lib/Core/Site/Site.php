<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSite;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;

class Site implements SiteInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Settings
     */
    private $settings;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private $domainObjectMapper;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $searchService;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $filteringSearchService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry
     */
    private $relationResolverRegistry;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\RelationService
     */
    private $relationService;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Settings $settings
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \eZ\Publish\API\Repository\SearchService $filteringSearchService
     * @param \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry $relationResolverRegistry
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     */
    public function __construct(
        BaseSite $settings,
        Repository $repository,
        SearchService $filteringSearchService,
        RelationResolverRegistry $relationResolverRegistry,
        QueryTypeRegistry $queryTypeRegistry
    ) {
        $this->settings = $settings;
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->locationService = $repository->getLocationService();
        $this->searchService = $repository->getSearchService();
        $this->filteringSearchService = $filteringSearchService;
        $this->relationResolverRegistry = $relationResolverRegistry;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getFilterService()
    {
        if ($this->filterService === null) {
            $this->filterService = new FilterService(
                $this->settings,
                $this->getDomainObjectMapper(),
                $this->filteringSearchService,
                $this->contentService
            );
        }

        return $this->filterService;
    }

    public function getFindService()
    {
        if ($this->findService === null) {
            $this->findService = new FindService(
                $this->settings,
                $this->getDomainObjectMapper(),
                $this->searchService,
                $this->contentService
            );
        }

        return $this->findService;
    }

    public function getLoadService()
    {
        if ($this->loadService === null) {
            $this->loadService = new LoadService(
                $this->settings,
                $this->getDomainObjectMapper(),
                $this->contentService,
                $this->locationService
            );
        }

        return $this->loadService;
    }

    public function getRelationService()
    {
        if ($this->relationService === null) {
            $this->relationService = new RelationService(
                $this,
                $this->relationResolverRegistry
            );
        }

        return $this->relationService;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private function getDomainObjectMapper()
    {
        if ($this->domainObjectMapper === null) {
            $this->domainObjectMapper = new DomainObjectMapper(
                $this,
                $this->repository,
                $this->queryTypeRegistry
            );
        }

        return $this->domainObjectMapper;
    }
}
