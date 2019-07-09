<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use DateTime;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\QueryType\QueryType;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor;
use Netgen\EzPlatformSiteApi\API\FilterService;
use Netgen\EzPlatformSiteApi\API\FindService;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FindAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class QueryExecutorTest extends TestCase
{
    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteContentFilterQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'content_query_type',
                'parameters' => ['parameters'],
                'useFilter' => true,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            false
        );

        $this->assertEquals($this->getFilterContentResult(), $result);
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteContentPagedFilterQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'content_query_type',
                'parameters' => ['parameters'],
                'useFilter' => true,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            true
        );

        $this->assertInstanceOf(Pagerfanta::class, $result);
        $this->assertInstanceOf(FilterAdapter::class, $result->getAdapter());
        $this->assertEquals(20, $result->getMaxPerPage());
        $this->assertEquals(2, $result->getCurrentPage());
        $this->assertTrue($result->getNormalizeOutOfRangePages());
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteContentFindQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'content_query_type',
                'parameters' => ['parameters'],
                'useFilter' => false,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            false
        );

        $this->assertEquals($this->getFindContentResult(), $result);
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteContentPagedFindQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'content_query_type',
                'parameters' => ['parameters'],
                'useFilter' => false,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            true
        );

        $this->assertInstanceOf(Pagerfanta::class, $result);
        $this->assertInstanceOf(FindAdapter::class, $result->getAdapter());
        $this->assertEquals(20, $result->getMaxPerPage());
        $this->assertEquals(2, $result->getCurrentPage());
        $this->assertTrue($result->getNormalizeOutOfRangePages());
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteLocationFilterQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'location_query_type',
                'parameters' => ['parameters'],
                'useFilter' => true,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            false
        );

        $this->assertEquals($this->getFilterLocationsResult(), $result);
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteLocationPagedFilterQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'location_query_type',
                'parameters' => ['parameters'],
                'useFilter' => true,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            true
        );

        $this->assertInstanceOf(Pagerfanta::class, $result);
        $this->assertInstanceOf(FilterAdapter::class, $result->getAdapter());
        $this->assertEquals(20, $result->getMaxPerPage());
        $this->assertEquals(2, $result->getCurrentPage());
        $this->assertTrue($result->getNormalizeOutOfRangePages());
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteLocationFindQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'location_query_type',
                'parameters' => ['parameters'],
                'useFilter' => false,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            false
        );

        $this->assertEquals($this->getFindLocationsResult(), $result);
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteLocationPagedFindQuery()
    {
        $executor = $this->getQueryExecutorUnderTest();
        $result = $executor->execute(
            new QueryDefinition([
                'name' => 'location_query_type',
                'parameters' => ['parameters'],
                'useFilter' => false,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            true
        );

        $this->assertInstanceOf(Pagerfanta::class, $result);
        $this->assertInstanceOf(FindAdapter::class, $result->getAdapter());
        $this->assertEquals(20, $result->getMaxPerPage());
        $this->assertEquals(2, $result->getCurrentPage());
        $this->assertTrue($result->getNormalizeOutOfRangePages());
    }

    /**
     * @throws \Pagerfanta\Exception\Exception
     */
    public function testExecuteThrowsRuntimeException()
    {
        $this->expectException(RuntimeException::class);

        $executor = $this->getQueryExecutorUnderTest();
        $executor->execute(
            new QueryDefinition([
                'name' => 'invalid_query_type',
                'parameters' => ['parameters'],
                'useFilter' => false,
                'maxPerPage' => 20,
                'page' => 2,
            ]),
            false
        );
    }

    /**
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor
     */
    protected function getQueryExecutorUnderTest()
    {
        return new QueryExecutor(
            $this->getQueryTypeRegistryMock(),
            $this->getFilterServiceMock(),
            $this->getFindServiceMock()
        );
    }

    protected function getFilterContentResult()
    {
        return new SearchResult([
            'totalCount' => 100,
            'searchHits' => ['FILTER CONTENT'],
        ]);
    }

    protected function getFindContentResult()
    {
        return new SearchResult([
            'totalCount' => 100,
            'searchHits' => ['FIND CONTENT'],
        ]);
    }

    protected function getFilterLocationsResult()
    {
        return new SearchResult([
            'totalCount' => 100,
            'searchHits' => ['FILTER LOCATIONS'],
        ]);
    }

    protected function getFindLocationsResult()
    {
        return new SearchResult([
            'totalCount' => 100,
            'searchHits' => ['FIND LOCATIONS'],
        ]);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\FilterService
     */
    protected function getFilterServiceMock()
    {
        $filterServiceMock = $this->getMockBuilder(FilterService::class)->getMock();
        $filterServiceMock
            ->expects($this->any())
            ->method('filterContent')
            ->willReturn($this->getFilterContentResult());
        $filterServiceMock
            ->expects($this->any())
            ->method('filterLocations')
            ->willReturn($this->getFilterLocationsResult());

        return $filterServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\FindService
     */
    protected function getFindServiceMock()
    {
        $findServiceMock = $this->getMockBuilder(FindService::class)->getMock();
        $findServiceMock
            ->expects($this->any())
            ->method('findContent')
            ->willReturn($this->getFindContentResult());
        $findServiceMock
            ->expects($this->any())
            ->method('findLocations')
            ->willReturn($this->getFindLocationsResult());

        return $findServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    protected function getQueryTypeRegistryMock()
    {
        $queryTypeRegistryMock = $this->getMockBuilder(QueryTypeRegistry::class)->getMock();

        $queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->willReturnMap([
                ['content_query_type', $this->getContentQueryTypeMock()],
                ['location_query_type', $this->getLocationQueryTypeMock()],
                ['invalid_query_type', $this->getInvalidQueryTypeMock()],
            ]);

        return $queryTypeRegistryMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContentQueryTypeMock()
    {
        $mock = $this->getMockBuilder(QueryType::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getQuery')
            ->willReturn(new Query());

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getLocationQueryTypeMock()
    {
        $mock = $this->getMockBuilder(QueryType::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getQuery')
            ->willReturn(new LocationQuery());

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getInvalidQueryTypeMock()
    {
        $mock = $this->getMockBuilder(QueryType::class)->getMock();
        $mock
            ->expects($this->any())
            ->method('getQuery')
            ->willReturn(new DateTime());

        return $mock;
    }
}
