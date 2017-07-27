<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;

class Site implements SiteInterface
{
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
    private $synchronousSearchService;

    /**
     * @var array
     */
    private $prioritizedLanguages;

    /**
     * @var bool
     */
    private $useAlwaysAvailable;

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
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\API\Repository\SearchService $synchronousSearchService
     * @param string[] $prioritizedLanguages
     * @param bool $useAlwaysAvailable
     */
    public function __construct(
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService,
        LocationService $locationService,
        SearchService $searchService,
        SearchService $synchronousSearchService,
        $useAlwaysAvailable
    ) {
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->synchronousSearchService = $synchronousSearchService;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
    }

    /**
     * Setter for prioritized languages configuration, used to update
     * the configuration on scope change.
     *
     * @param array $prioritizedLanguages
     */
    public function setPrioritizedLanguages(array $prioritizedLanguages)
    {
        $this->prioritizedLanguages = $prioritizedLanguages;
    }

    public function getFilterService()
    {
        if ($this->filterService !== null) {
            return $this->filterService;
        }

        $this->filterService = new FilterService(
            $this->getDomainObjectMapper(),
            $this->synchronousSearchService,
            $this->contentService,
            $this->prioritizedLanguages,
            $this->useAlwaysAvailable
        );

        return $this->filterService;
    }

    public function getFindService()
    {
        if ($this->findService !== null) {
            return $this->findService;
        }

        $this->findService = new FindService(
            $this->getDomainObjectMapper(),
            $this->searchService,
            $this->contentService,
            $this->prioritizedLanguages,
            $this->useAlwaysAvailable
        );

        return $this->findService;
    }

    public function getLoadService()
    {
        if ($this->loadService !== null) {
            return $this->loadService;
        }

        $this->loadService = new LoadService(
            $this->getDomainObjectMapper(),
            $this->contentService,
            $this->locationService,
            $this->prioritizedLanguages,
            $this->useAlwaysAvailable
        );

        return $this->loadService;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private function getDomainObjectMapper()
    {
        if ($this->domainObjectMapper !== null) {
            return $this->domainObjectMapper;
        }

        $this->domainObjectMapper = new DomainObjectMapper(
            $this,
            $this->contentTypeService,
            $this->fieldTypeService
        );

        return $this->domainObjectMapper;
    }
}
