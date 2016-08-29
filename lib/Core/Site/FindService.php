<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\FindService as FindServiceInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

final class FindService implements FindServiceInterface
{
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
     * @var array
     */
    private $prioritizedLanguages;

    /**
     * @var bool
     */
    private $useAlwaysAvailable;

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper $domainObjectMapper
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param array $prioritizedLanguages
     * @param bool $useAlwaysAvailable
     */
    public function __construct(
        DomainObjectMapper $domainObjectMapper,
        SearchService $searchService,
        ContentService $contentService,
        array $prioritizedLanguages,
        $useAlwaysAvailable
    ) {
        $this->domainObjectMapper = $domainObjectMapper;
        $this->searchService = $searchService;
        $this->contentService = $contentService;
        $this->prioritizedLanguages = $prioritizedLanguages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
    }

    public function findContent(Query $query)
    {
        $searchResult = $this->searchService->findContentInfo(
            $query,
            [
                'languages' => $this->prioritizedLanguages,
                'useAlwaysAvailable' => $this->useAlwaysAvailable,
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
                'languages' => $this->prioritizedLanguages,
                'useAlwaysAvailable' => $this->useAlwaysAvailable,
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
                'languages' => $this->prioritizedLanguages,
                'useAlwaysAvailable' => $this->useAlwaysAvailable,
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
        $searchResult = $this->searchService->findLocations(
            $query,
            [
                'languages' => $this->prioritizedLanguages,
                'useAlwaysAvailable' => $this->useAlwaysAvailable,
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
