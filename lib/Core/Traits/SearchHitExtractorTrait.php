<?php

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

trait SearchHitExtractorTrait
{
    /**
     * Extracts Content/Location value object from SearchResult
     *
     * @param SearchResult $searchResult
     *
     * @return array
     */
    protected function extractValueObject(SearchResult $searchResult)
    {
        return array_map(
            function(SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );
    }
}
