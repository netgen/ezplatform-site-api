<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

class SearchResultExtractorStub
{
    use SearchResultExtractorTrait;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return
     */
    public function extract(SearchResult $searchResult)
    {
        return $this->extractValueObjects($searchResult);
    }
}
