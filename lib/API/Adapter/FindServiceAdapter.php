<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Adapter;

use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\SPI\Search\Capable;
use eZ\Publish\SPI\Search\Handler;
use Netgen\EzPlatformSiteApi\API\FindService;

/**
 * This class is an adapter from Site API find service to SearchService interface
 * from eZ Publish kernel. The point is being able to replace usage of eZ search service
 * with Site API find service without touching consuming code.
 *
 * Methods implemented here do not use $languageFilter argument since it is handled automatically
 * by the find service itself.
 *
 * As for $filterOnUserPermissions, find service doesn't support it, so it is simply ignored.
 */
final class FindServiceAdapter implements SearchService
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var \eZ\Publish\SPI\Search\Handler
     */
    private $searchHandler;

    public function __construct(FindService $findService, Handler $searchHandler)
    {
        $this->findService = $findService;
        $this->searchHandler = $searchHandler;
    }

    public function findContent(Query $query, array $languageFilter = [], $filterOnUserPermissions = true): SearchResult
    {
        $searchResult = $this->findService->findContent($query);

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $siteContent */
            $siteContent = $searchHit->valueObject;
            $searchHit->valueObject = $siteContent->innerContent;
        }

        return $searchResult;
    }

    public function findContentInfo(Query $query, array $languageFilter = [], $filterOnUserPermissions = true): SearchResult
    {
        $searchResult = $this->findService->findContent($query);

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $siteContent */
            $siteContent = $searchHit->valueObject;
            $searchHit->valueObject = $siteContent->contentInfo->innerContentInfo;
        }

        return $searchResult;
    }

    public function findLocations(LocationQuery $query, array $languageFilter = [], $filterOnUserPermissions = true): SearchResult
    {
        $searchResult = $this->findService->findLocations($query);

        foreach ($searchResult->searchHits as $searchHit) {
            /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $siteLocation */
            $siteLocation = $searchHit->valueObject;
            $searchHit->valueObject = $siteLocation->innerLocation;
        }

        return $searchResult;
    }

    public function findSingle(Criterion $filter, array $languageFilter = [], $filterOnUserPermissions = true): Content
    {
        $query = new Query();
        $query->filter = $filter;
        $query->limit = 1;

        $searchResult = $this->findService->findContent($query);

        if ($searchResult->totalCount === 0) {
            throw new NotFoundException('Content', 'findSingle() found no content for given $filter');
        }

        if ($searchResult->totalCount > 1) {
            throw new InvalidArgumentException('totalCount', 'findSingle() found more then one item for given $filter');
        }

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $siteContent */
        $siteContent = $searchResult->searchHits[0]->valueObject;

        return $siteContent->innerContent;
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null): void
    {
    }

    public function supports($capabilityFlag): bool
    {
        if ($this->searchHandler instanceof Capable) {
            return $this->searchHandler->supports($capabilityFlag);
        }

        return false;
    }
}
