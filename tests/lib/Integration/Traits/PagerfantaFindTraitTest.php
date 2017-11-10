<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchHitAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter;
use Netgen\EzPlatformSiteApi\Tests\Integration\BaseTest;

class PagerfantaFindTraitTest extends BaseTest
{
    protected $stub;

    public function setUp()
    {
        parent::setUp();
        $this->stub = new PagerFantaFindStub($this->getSite());
    }

    public function testCreateContentSearchHitPager()
    {
        $query = new Query();
        $currentPage = 1;
        $maxPerPage = 10;
        $pager = $this->stub->getContentSearchHitPager($query, 1, 10);

        $this->assertInstanceOf(ContentSearchHitAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(ContentSearchAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(LocationSearchHitAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(LocationSearchAdapter::class, $pager->getAdapter());
        $this->assertEquals($currentPage, $pager->getCurrentPage());
        $this->assertEquals($maxPerPage, $pager->getMaxPerPage());
    }

    public function testCreateContentSearchPager()
    {
        $query = new Query();
        $currentPage = 1;
        $maxPerPage = 10;
        $pager = $this->stub->getContentSearchPager($query, 1, 10);

        $this->assertInstanceOf(ContentSearchAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(LocationSearchHitAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(LocationSearchAdapter::class, $pager->getAdapter());
        $this->assertEquals($currentPage, $pager->getCurrentPage());
        $this->assertEquals($maxPerPage, $pager->getMaxPerPage());
    }

    public function testCreateLocationSearchHitPager()
    {
        $query = new LocationQuery();
        $currentPage = 1;
        $maxPerPage = 10;
        $pager = $this->stub->getLocationSearchHitPager($query, 1, 10);

        $this->assertInstanceOf(LocationSearchHitAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(ContentSearchAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(ContentSearchHitAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(LocationSearchAdapter::class, $pager->getAdapter());
        $this->assertEquals($currentPage, $pager->getCurrentPage());
        $this->assertEquals($maxPerPage, $pager->getMaxPerPage());
    }

    public function testCreateLocationSearchPager()
    {
        $query = new LocationQuery();
        $currentPage = 1;
        $maxPerPage = 10;
        $pager = $this->stub->getLocationSearchPager($query, 1, 10);

        $this->assertInstanceOf(LocationSearchAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(ContentSearchAdapter::class, $pager->getAdapter());
        $this->assertNotInstanceOf(ContentSearchHitAdapter::class, $pager->getAdapter());
        $this->assertEquals($currentPage, $pager->getCurrentPage());
        $this->assertEquals($maxPerPage, $pager->getMaxPerPage());
    }
}
