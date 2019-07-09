<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

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
    protected function extractValueObjects(SearchResult $searchResult)
    {
        return array_map(
            function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );
    }
}
