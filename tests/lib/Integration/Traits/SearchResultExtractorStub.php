<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;

class SearchResultExtractorStub
{
    use SearchResultExtractorTrait;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    public function extract(SearchResult $searchResult): array
    {
        return $this->extractValueObjects($searchResult);
    }
}
