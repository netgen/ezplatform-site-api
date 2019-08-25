<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSearchExtra\Core\Pagination\Pagerfanta\Slice;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FindAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group pager
 *
 * @internal
 */
final class FindAdapterTest extends TestCase
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $findService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->findService = $this->getMockBuilder(FindService::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
    }

    public function testGetNbResults(): void
    {
        $nbResults = 123;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['totalCount' => $nbResults]);

        $this->findService
            ->expects($this->once())
            ->method('findContent')
            ->with($this->equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        $this->assertSame($nbResults, $adapter->getNbResults());
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetFacets(): void
    {
        $facets = ['facet', 'facet'];
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['facets' => $facets]);

        $this->findService
            ->expects($this->once())
            ->method('findContent')
            ->with($this->equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        $this->assertSame($facets, $adapter->getFacets());
        $this->assertSame($facets, $adapter->getFacets());
    }

    public function testMaxScore(): void
    {
        $maxScore = 100;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['maxScore' => $maxScore]);

        $this->findService
            ->expects($this->once())
            ->method('findContent')
            ->with($this->equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        $this->assertSame($maxScore, $adapter->getMaxScore());
        $this->assertSame($maxScore, $adapter->getMaxScore());
    }

    public function testTimeIsNotSet(): void
    {
        $this->findService
            ->expects($this->never())
            ->method('findContent');

        $adapter = $this->getAdapter(new Query());

        $this->assertNull($adapter->getTime());
        $this->assertNull($adapter->getTime());
    }

    public function testGetSlice(): void
    {
        $offset = 20;
        $limit = 25;
        $nbResults = 123;
        $facets = ['facet', 'facet'];
        $maxScore = 100;
        $time = 256;
        $query = new Query(['offset' => 5, 'limit' => 10]);
        $searchQuery = clone $query;
        $searchQuery->offset = $offset;
        $searchQuery->limit = $limit;
        $searchQuery->performCount = false;

        $hits = [new SearchHit(['valueObject' => 'Content'])];
        $searchResult = new SearchResult([
            'searchHits' => $hits,
            'totalCount' => $nbResults,
            'facets' => $facets,
            'maxScore' => $maxScore,
            'time' => $time,
        ]);

        $this->findService
            ->expects($this->once())
            ->method('findContent')
            ->with($this->equalTo($searchQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);
        $slice = $adapter->getSlice($offset, $limit);

        $this->assertInstanceOf(Slice::class, $slice);
        $this->assertSame($hits, $slice->getSearchHits());
        $this->assertSame($nbResults, $adapter->getNbResults());
        $this->assertSame($facets, $adapter->getFacets());
        $this->assertSame($maxScore, $adapter->getMaxScore());
        $this->assertSame($time, $adapter->getTime());
    }

    public function testLocationQuery(): void
    {
        $query = new LocationQuery(['performCount' => false]);

        $this->findService
            ->expects($this->once())
            ->method('findLocations')
            ->with($this->equalTo($query))
            ->willReturn(new SearchResult());

        $adapter = $this->getAdapter($query);
        $adapter->getSlice(0, 25);
    }

    protected function getAdapter(Query $query): FindAdapter
    {
        return new FindAdapter($query, $this->findService);
    }
}
