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
    public function doExtractValueObjects(SearchResult $searchResult): array
    {
        return $this->extractValueObjects($searchResult);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    public function doExtractContentItems(SearchResult $searchResult): array
    {
        return $this->extractContentItems($searchResult);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    public function doExtractLocations(SearchResult $searchResult): array
    {
        return $this->extractLocations($searchResult);
    }
}
