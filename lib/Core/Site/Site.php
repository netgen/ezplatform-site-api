<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\UserService;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSite;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;

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
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

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
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

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
     * @param \Netgen\EzPlatformSiteApi\API\Settings $settings
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\API\Repository\SearchService $filteringSearchService
     * @param \eZ\Publish\API\Repository\UserService $userService
     */
    public function __construct(
        BaseSite $settings,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService,
        LocationService $locationService,
        SearchService $searchService,
        SearchService $filteringSearchService,
        UserService $userService
    ) {
        $this->settings = $settings;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->filteringSearchService = $filteringSearchService;
        $this->userService = $userService;
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
            $this->relationService = new RelationService($this);
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
                $this->contentService,
                $this->contentTypeService,
                $this->fieldTypeService,
                $this->userService
            );
        }

        return $this->domainObjectMapper;
    }
}
