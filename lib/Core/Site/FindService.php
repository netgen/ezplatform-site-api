<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\FindService as FindServiceInterface;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSettings;

class FindService implements FindServiceInterface
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
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $searchService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Settings $settings
     * @param \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper $domainObjectMapper
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        BaseSettings $settings,
        DomainObjectMapper $domainObjectMapper,
        SearchService $searchService,
        ContentService $contentService
    ) {
        $this->settings = $settings;
        $this->domainObjectMapper = $domainObjectMapper;
        $this->searchService = $searchService;
        $this->contentService = $contentService;
    }

    public function findContent(Query $query)
    {
        $searchResult = $this->searchService->findContentInfo(
            $query,
            [
                'languages' => $this->settings->prioritizedLanguages,
                'useAlwaysAvailable' => $this->settings->useAlwaysAvailable,
            ]
        );

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $searchHit->valueObject;
            $searchHit->valueObject = $this->domainObjectMapper->mapContent(
                $this->contentService->loadContent(
                    $contentInfo->id,
                    [$searchHit->matchedTranslation]
                ),
                $searchHit->matchedTranslation
            );
        }

        return $searchResult;
    }

    public function findContentInfo(Query $query)
    {
        $searchResult = $this->searchService->findContentInfo(
            $query,
            [
                'languages' => $this->settings->prioritizedLanguages,
                'useAlwaysAvailable' => $this->settings->useAlwaysAvailable,
            ]
        );

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo */
            $contentInfo = $searchHit->valueObject;
            $searchHit->valueObject = $this->domainObjectMapper->mapContentInfo(
                $this->contentService->loadVersionInfo(
                    $contentInfo,
                    $contentInfo->currentVersionNo
                ),
                $searchHit->matchedTranslation
            );
        }

        return $searchResult;
    }

    public function findLocations(LocationQuery $query)
    {
        $searchResult = $this->searchService->findLocations(
            $query,
            [
                'languages' => $this->settings->prioritizedLanguages,
                'useAlwaysAvailable' => $this->settings->useAlwaysAvailable,
            ]
        );

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $searchHit->valueObject;
            $searchHit->valueObject = $this->domainObjectMapper->mapLocation(
                $location,
                $this->contentService->loadVersionInfo(
                    $location->contentInfo,
                    $location->contentInfo->currentVersionNo
                ),
                $searchHit->matchedTranslation
            );
        }

        return $searchResult;
    }

    public function findNodes(LocationQuery $query)
    {
        @trigger_error('findNodes() is deprecated since version 2.1 and will be removed in 3.0. Use findLocations() instead.', E_USER_DEPRECATED);

        $searchResult = $this->searchService->findLocations(
            $query,
            [
                'languages' => $this->settings->prioritizedLanguages,
                'useAlwaysAvailable' => $this->settings->useAlwaysAvailable,
            ]
        );

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $searchHit->valueObject;
            $searchHit->valueObject = $this->domainObjectMapper->mapNode(
                $location,
                $this->contentService->loadContent(
                    $location->contentInfo->id,
                    [$searchHit->matchedTranslation],
                    $location->contentInfo->currentVersionNo
                ),
                $searchHit->matchedTranslation
            );
        }

        return $searchResult;
    }
}
