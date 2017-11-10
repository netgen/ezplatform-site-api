<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Tests\Integration\BaseTest;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

class SearchResultExtractorTraitTest extends BaseTest
{
    protected $stub;

    public function setUp()
    {
        parent::setUp();
        $this->stub = new SearchResultExtractorStub();
    }

    public function testItExtractsValuesFromLocations()
    {
        $locationIds = [5, 56];
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationIds),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $locationValueObjects = $this->stub->extract($searchResult);

        foreach ($locationValueObjects as $value) {
            $this->assertInstanceOf(Location::class, $value);
        }
    }

    public function testItExtractsValuesFromEmptyLocationSearchResult()
    {
        $locationIds = [54];
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationIds),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $locationValueObjects = $this->stub->extract($searchResult);

        $this->assertInternalType('array', $locationValueObjects);
        $this->assertEmpty($locationValueObjects);
    }

    public function testItExtractsValuesFromContents()
    {
        $contentIds = [4, 54];
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentIds),
            ]
        );

        $searchResult = $findService->findContent($query);

        $contentValueObjects = $this->stub->extract($searchResult);

        foreach ($contentValueObjects as $value) {
            $this->assertInstanceOf(Content::class, $value);
        }
    }

    public function testItExtractsValuesFromEmptyContentSearchResult()
    {
        $contentIds = [52];
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentIds),
            ]
        );

        $searchResult = $findService->findContent($query);

        $contentValueObjects = $this->stub->extract($searchResult);

        $this->assertInternalType('array', $contentValueObjects);
        $this->assertEmpty($contentValueObjects);
    }
}
