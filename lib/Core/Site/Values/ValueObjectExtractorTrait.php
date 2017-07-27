<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * @internal
 */
trait ValueObjectExtractorTrait
{
    /**
     * Extracts value objects from the given $searchResult.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    private function extractValuesFromSearchResult(SearchResult $searchResult)
    {
        $valueObjects = [];

        foreach ($searchResult->searchHits as $searchHit) {
            $valueObjects[] = $searchHit->valueObject;
        }

        return $valueObjects;
    }
}
