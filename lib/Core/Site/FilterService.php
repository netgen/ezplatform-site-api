<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\FilterService as FilterServiceInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

class FilterService implements FilterServiceInterface
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

    public function filterContent(Query $query)
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

    public function filterContentInfo(Query $query)
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

    public function filterLocations(LocationQuery $query)
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
}
