<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Base class for FilterService API integration tests.
 */
class FilterServiceBaseTest extends BaseTest
{
    protected function doTestFilterContent($data)
    {
        list(, $contentId) = array_values($data);
        $filterService = $this->getSite()->getFilterService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentId),
            ]
        );

        $searchResult = $filterService->filterContent($query);

        $this->assertContentSearchResult($searchResult, $data);
    }

    protected function doTestFilterContentInfo($data)
    {
        list(, $contentId) = array_values($data);
        $filterService = $this->getSite()->getFilterService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentId),
            ]
        );

        $searchResult = $filterService->filterContentInfo($query);

        $this->assertContentInfoSearchResult($searchResult, $data);
    }

    protected function doTestFilterLocations($data)
    {
        list(, , , $locationId) = array_values($data);
        $filterService = $this->getSite()->getFilterService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationId),
            ]
        );

        $searchResult = $filterService->filterLocations($query);

        $this->assertLocationSearchResult($searchResult, $data);
    }

    protected function assertContentSearchResult(SearchResult $searchResult, $data)
    {
        if ($data['isFound'] === false) {
            $this->assertSame(0, $searchResult->totalCount);

            return;
        }

        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContent($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertContentInfoSearchResult(SearchResult $searchResult, $data)
    {
        if ($data['isFound'] === false) {
            $this->assertSame(0, $searchResult->totalCount);

            return;
        }

        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContentInfo($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertLocationSearchResult(SearchResult $searchResult, $data)
    {
        if ($data['isFound'] === false) {
            $this->assertSame(0, $searchResult->totalCount);

            return;
        }

        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertLocation($searchResult->searchHits[0]->valueObject, $data);
    }
}
