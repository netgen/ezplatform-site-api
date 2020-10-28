<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSearchExtra\Core\Pagination\Pagerfanta\Slice;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group pager
 *
 * @internal
 */
final class FilterAdapterTest extends TestCase
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $filterService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filterService = $this->getMockBuilder(FilterService::class)
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

        $this->filterService
            ->expects(self::once())
            ->method('filterContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($nbResults, $adapter->getNbResults());
        self::assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetFacets(): void
    {
        $facets = ['facet', 'facet'];
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['facets' => $facets]);

        $this->filterService
            ->expects(self::once())
            ->method('filterContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($facets, $adapter->getFacets());
        self::assertSame($facets, $adapter->getFacets());
    }

    public function testMaxScore(): void
    {
        $maxScore = 100.0;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['maxScore' => $maxScore]);

        $this->filterService
            ->expects(self::once())
            ->method('filterContent')
            ->with(self::equalTo($countQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);

        self::assertSame($maxScore, $adapter->getMaxScore());
        self::assertSame($maxScore, $adapter->getMaxScore());
    }

    public function testTimeIsNotSet(): void
    {
        $this->filterService
            ->expects(self::never())
            ->method('filterContent');

        $adapter = $this->getAdapter(new Query());

        self::assertNull($adapter->getTime());
        self::assertNull($adapter->getTime());
    }

    public function testGetSlice(): void
    {
        $offset = 20;
        $limit = 25;
        $nbResults = 123;
        $facets = ['facet', 'facet'];
        $maxScore = 100.0;
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

        $this->filterService
            ->expects(self::once())
            ->method('filterContent')
            ->with(self::equalTo($searchQuery))
            ->willReturn($searchResult);

        $adapter = $this->getAdapter($query);
        $slice = $adapter->getSlice($offset, $limit);

        self::assertInstanceOf(Slice::class, $slice);
        self::assertSame($hits, $slice->getSearchHits());
        self::assertSame($nbResults, $adapter->getNbResults());
        self::assertSame($facets, $adapter->getFacets());
        self::assertSame($maxScore, $adapter->getMaxScore());
        self::assertSame($time, $adapter->getTime());
    }

    public function testLocationQuery(): void
    {
        $query = new LocationQuery(['performCount' => false]);

        $this->filterService
            ->expects(self::once())
            ->method('filterLocations')
            ->with(self::equalTo($query))
            ->willReturn(new SearchResult());

        $adapter = $this->getAdapter($query);
        $adapter->getSlice(0, 25);
    }

    protected function getAdapter(Query $query): FilterAdapter
    {
        return new FilterAdapter($query, $this->filterService);
    }
}
