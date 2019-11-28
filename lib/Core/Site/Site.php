<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\SearchService;
use Netgen\EzPlatformSiteApi\API\FilterService as APIFilterService;
use Netgen\EzPlatformSiteApi\API\FindService as APIFindService;
use Netgen\EzPlatformSiteApi\API\LoadService as APILoadService;
use Netgen\EzPlatformSiteApi\API\RelationService as APIRelationService;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSettings;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @final
 *
 * @internal
 *
 * Hint against API interface instead of this service:
 *
 * @see \Netgen\EzPlatformSiteApi\API\Site
 */
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        BaseSettings $settings,
        Repository $repository,
        SearchService $filteringSearchService,
        RelationResolverRegistry $relationResolverRegistry,
        ?LoggerInterface $logger = null
    ) {
        $this->settings = $settings;
        $this->repository = $repository;
        $this->contentService = $repository->getContentService();
        $this->locationService = $repository->getLocationService();
        $this->searchService = $repository->getSearchService();
        $this->filteringSearchService = $filteringSearchService;
        $this->relationResolverRegistry = $relationResolverRegistry;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getSettings(): BaseSettings
    {
        return $this->settings;
    }

    public function getFilterService(): APIFilterService
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

    public function getFindService(): APIFindService
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

    public function getLoadService(): APILoadService
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

    public function getRelationService(): APIRelationService
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
     * @internal for Site API internal use only
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    public function getDomainObjectMapper(): DomainObjectMapper
    {
        if ($this->domainObjectMapper === null) {
            $this->domainObjectMapper = new DomainObjectMapper(
                $this,
                $this->repository,
                $this->settings->failOnMissingFields,
                $this->logger
            );
        }

        return $this->domainObjectMapper;
    }
}
