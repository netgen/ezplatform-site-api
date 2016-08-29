<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentInfoSearchHitAdapter;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use PHPUnit_Framework_TestCase;

class ContentInfoSearchHitAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $findService;

    protected function setUp()
    {
        parent::setUp();
        $this->findService = $this->getMock('\Netgen\EzPlatformSiteApi\API\FindService');
    }

    protected function getAdapter(Query $query)
    {
        return new ContentInfoSearchHitAdapter($query, $this->findService);
    }

    public function testGetNbResults()
    {
        $nbResults = 123;
        $query = new Query(['limit' => 10]);
        $countQuery = clone $query;
        $countQuery->limit = 0;
        $searchResult = new SearchResult(['totalCount' => $nbResults]);

        $this->findService
            ->expects($this->once())
            ->method('findContentInfo')
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

        $hits = [new SearchHit(['valueObject' => 'ContentInfo'])];
        $searchResult = new SearchResult(['searchHits' => $hits, 'totalCount' => $nbResults]);

        $this->findService
            ->expects($this->once())
            ->method('findContentInfo')
            ->with($this->equalTo($searchQuery))
            ->will($this->returnValue($searchResult));

        $adapter = $this->getAdapter($query);

        $this->assertSame($this->getExpectedSlice($hits), $adapter->getSlice($offset, $limit));
        $this->assertSame($nbResults, $adapter->getNbResults());
    }

    protected function getExpectedSlice($hits)
    {
        return $hits;
    }
}
