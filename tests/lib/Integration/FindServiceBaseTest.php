<?php

namespace Netgen\EzPlatformSite\Tests\Integration;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Base class for FindService API integration tests.
 */
class FindServiceBaseTest extends BaseTest
{
    protected function doTestFindContent($data)
    {
        list(, $contentId) = array_values($data);
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentId),
            ]
        );

        $searchResult = $findService->findContent($query);

        $this->assertContentSearchResult($searchResult, $data);
    }

    protected function doTestFindContentInfo($data)
    {
        list(, $contentId) = array_values($data);
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentId),
            ]
        );

        $searchResult = $findService->findContentInfo($query);

        $this->assertContentInfoSearchResult($searchResult, $data);
    }

    protected function doTestFindLocations($data)
    {
        list(, , , $locationId) = array_values($data);
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationId),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $this->assertLocationSearchResult($searchResult, $data);
    }

    protected function doTestFindNodes($data)
    {
        list(, , , $locationId) = array_values($data);
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationId),
            ]
        );

        $searchResult = $findService->findNodes($query);

        $this->assertNodeSearchResult($searchResult, $data);
    }

    protected function assertContentSearchResult(SearchResult $searchResult, $data)
    {
        if ($data['isFound'] === false) {
            $this->assertSame(0, $searchResult->totalCount);

            return;
        }

        list(, , , , , , , $languageCode) = array_values($data);

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

        list(, , , , , , , $languageCode) = array_values($data);

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

        list(, , , , , , , $languageCode) = array_values($data);

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertLocation($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertNodeSearchResult(SearchResult $searchResult, $data)
    {
        if ($data['isFound'] === false) {
            $this->assertSame(0, $searchResult->totalCount);

            return;
        }

        list(, , , , , , , $languageCode) = array_values($data);

        $this->assertSame(1, $searchResult->totalCount);

        /* @var \Netgen\EzPlatformSite\API\Values\Node */
        $node = $searchResult->searchHits[0]->valueObject;

        $this->assertInstanceOf('\Netgen\EzPlatformSite\API\Values\Node', $node);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContent($node, $data);
        $this->assertLocation($node->location, $data);
    }
}
