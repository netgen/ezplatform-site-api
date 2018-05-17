<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter;
use PHPUnit\Framework\TestCase;

/**
 * @group pager
 */
class ContentSearchFilterAdapterTest extends TestCase
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterService;

    protected function setUp()
    {
        parent::setUp();
        $this->filterService = $this->getMockBuilder(FilterService::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
    }

    public function testGetNbResults()
    {
        $nbResults = 123;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['totalCount' => $nbResults]);

        $this->filterService
            ->expects($this->once())
            ->method('filterContent')
            ->with($this->equalTo($countQuery))
            ->will($this->returnValue($searchResult));

        $adapter = $this->getAdapter($query);

        $this->assertSame($nbResults, $adapter->getNbResults());
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    public function testGetSlice()
    {
        $offset = 20;
        $limit = 25;
        $nbResults = 123;
        $query = new Query(['offset' => 5, 'limit' => 10]);

        $searchQuery = clone $query;
        $searchQuery->offset = $offset;
        $searchQuery->limit = $limit;
        $searchQuery->performCount = false;

        $hits = [new SearchHit(['valueObject' => 'Location'])];
        $searchResult = new SearchResult(['searchHits' => $hits, 'totalCount' => $nbResults]);

        $this->filterService
            ->expects($this->once())
            ->method('filterContent')
            ->with($this->equalTo($searchQuery))
            ->will($this->returnValue($searchResult));

        $adapter = $this->getAdapter($query);

        $this->assertSame($this->getExpectedSlice($hits), $adapter->getSlice($offset, $limit));
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    protected function getAdapter(Query $query)
    {
        return new ContentSearchFilterAdapter($query, $this->filterService);
    }

    protected function getExpectedSlice($hits)
    {
        $expectedResult = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[] $hits */
        foreach ($hits as $hit) {
            $expectedResult[] = $hit->valueObject;
        }

        return $expectedResult;
    }
}
