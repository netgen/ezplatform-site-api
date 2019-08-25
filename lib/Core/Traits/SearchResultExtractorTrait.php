<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

/**
 * SearchResultExtractorTrait provides a way to extract value objects
 * (usually Content items or Locations) for eZ Platform SearchResult.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
 */
trait SearchResultExtractorTrait
{
    /**
     * Extracts value objects from SearchResult.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    protected function extractValueObjects(SearchResult $searchResult): array
    {
        return \array_map(
            static function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );
    }

    /**
     * Extracts Content items from SearchResult.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    protected function extractContentItems(SearchResult $searchResult): array
    {
        return \array_map(
            static function (SearchHit $searchHit): Content {
                /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
                $content = $searchHit->valueObject;

                return $content;
            },
            $searchResult->searchHits
        );
    }

    /**
     * Extracts Locations from SearchResult.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    protected function extractLocations(SearchResult $searchResult): array
    {
        return \array_map(
            static function (SearchHit $searchHit): Location {
                /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
                $location = $searchHit->valueObject;

                return $location;
            },
            $searchResult->searchHits
        );
    }
}
