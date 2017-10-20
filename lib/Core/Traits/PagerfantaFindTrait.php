<?php

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchHitAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchHitAdapter;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

trait PagerfantaFindTrait
{
    /**
     * Returns Pagerfanta pager that starts from first page
     * configured with
     * ContentSearchAdapter and FindService
     *
     * @param Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createContentSearchPager(Query $query, $currentPage, $maxPerPage)
    {
        $adapter = new ContentSearchAdapter($query, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $currentPage, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * configured with
     * ContentSearchHitAdapter and FindService
     *
     * @param Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createContentSearchHitPager(Query $query, $currentPage, $maxPerPage)
    {
        $adapter = new ContentSearchHitAdapter($query, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $currentPage, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * configured with
     * LocationSearchAdapter and FindService
     *
     * @param LocationQuery $locationQuery
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createLocationSearchPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)
    {
        $adapter = new LocationSearchAdapter($locationQuery, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $currentPage, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * configured with
     * LocationSearchHitAdapter and FindService
     *
     * @param LocationQuery $locationQuery
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createLocationSearchHitPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)
    {
        $adapter = new LocationSearchHitAdapter($locationQuery, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $currentPage, $maxPerPage);
    }

    /**
     * Shorthand method for creating Pagerfanta pager
     * with preconfigured Adapter
     *
     * @param AdapterInterface $adapter
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function getPagerfantaPager(AdapterInterface $adapter, $currentPage, $maxPerPage)
    {
        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
