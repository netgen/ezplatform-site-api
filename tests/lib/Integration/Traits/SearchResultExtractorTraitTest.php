<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Tests\Integration\BaseTest;

/**
 * @internal
 */
final class SearchResultExtractorTraitTest extends BaseTest
{
    /**
     * @var \Netgen\EzPlatformSiteApi\Tests\Integration\Traits\SearchResultExtractorStub
     */
    protected $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = new SearchResultExtractorStub();
    }

    public function testItExtractsValuesFromLocationSearchResult(): void
    {
        $locationIds = [5, 56];
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationIds),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $locationValueObjects = $this->stub->doExtractValueObjects($searchResult);

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

        $locationValueObjects = $this->stub->doExtractValueObjects($searchResult);

        $this->assertIsArray($locationValueObjects);
        $this->assertEmpty($locationValueObjects);
    }

    public function testItExtractsValuesFromContentSearchResult(): void
    {
        $contentIds = [4, 54];
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentIds),
            ]
        );

        $searchResult = $findService->findContent($query);

        $contentValueObjects = $this->stub->doExtractValueObjects($searchResult);

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

        $contentValueObjects = $this->stub->doExtractValueObjects($searchResult);

        $this->assertIsArray($contentValueObjects);
        $this->assertEmpty($contentValueObjects);
    }

    public function testItExtractsContentItemsFromContentSearchResult(): void
    {
        $contentIds = [4, 41];
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentIds),
            ]
        );

        $searchResult = $findService->findContent($query);

        $contentValueObjects = $this->stub->doExtractContentItems($searchResult);

        $this->assertCount(2, $contentValueObjects);

        foreach ($contentValueObjects as $value) {
            $this->assertInstanceOf(Content::class, $value);
        }
    }

    public function testItExtractsContentItemsFromEmptyContentSearchResult(): void
    {
        $contentIds = [52];
        $findService = $this->getSite()->getFindService();
        $query = new Query(
            [
                'filter' => new Query\Criterion\ContentId($contentIds),
            ]
        );

        $searchResult = $findService->findContent($query);

        $contentValueObjects = $this->stub->doExtractContentItems($searchResult);

        $this->assertIsArray($contentValueObjects);
        $this->assertEmpty($contentValueObjects);
    }

    public function testItExtractsLocationsFromLocationSearchResult(): void
    {
        $locationIds = [5, 12];
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationIds),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $locationValueObjects = $this->stub->doExtractLocations($searchResult);

        $this->assertCount(2, $locationValueObjects);

        foreach ($locationValueObjects as $value) {
            $this->assertInstanceOf(Location::class, $value);
        }
    }

    public function testItExtractsLocationsFromEmptyLocationSearchResult(): void
    {
        $locationIds = [54];
        $findService = $this->getSite()->getFindService();
        $query = new LocationQuery(
            [
                'filter' => new Query\Criterion\LocationId($locationIds),
            ]
        );

        $searchResult = $findService->findLocations($query);

        $locationValueObjects = $this->stub->doExtractLocations($searchResult);

        $this->assertIsArray($locationValueObjects);
        $this->assertEmpty($locationValueObjects);
    }
}
