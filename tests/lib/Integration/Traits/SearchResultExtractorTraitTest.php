<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Tests\Integration\BaseTest;

class SearchResultExtractorTraitTest extends BaseTest
{
    /**
     * @var \Netgen\EzPlatformSiteApi\Tests\Integration\Traits\SearchResultExtractorStub
     */
    protected $stub;

    public function setUp(): void
    {
        parent::setUp();
        $this->stub = new SearchResultExtractorStub();
    }

    public function testItExtractsValuesFromLocations(): void
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

    public function testItExtractsValuesFromEmptyLocationSearchResult(): void
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

    public function testItExtractsValuesFromContents(): void
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

    public function testItExtractsValuesFromEmptyContentSearchResult(): void
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
