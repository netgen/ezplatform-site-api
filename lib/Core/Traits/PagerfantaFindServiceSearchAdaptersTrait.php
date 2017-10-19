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
use Symfony\Component\HttpFoundation\Request;

trait PagerfantaFindServiceSearchAdaptersTrait
{
    /**
     * Returns Pagerfanta pager that starts from first page
     * and uses 'page' GET parameter from Request object
     * configured with
     * ContentSearchAdapter and FindService
     *
     * @param Query $query
     * @param Request $request
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createContentSearchPager(Query $query, Request $request, $maxPerPage)
    {
        $adapter = new ContentSearchAdapter($query, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $request, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * and uses 'page' GET parameter from Request object
     * configured with
     * ContentSearchHitAdapter and FindService
     *
     * @param Query $query
     * @param Request $request
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createContentSearchHitPager(Query $query, Request $request, $maxPerPage)
    {
        $adapter = new ContentSearchHitAdapter($query, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $request, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * and uses 'page' GET parameter from Request object
     * configured with
     * LocationSearchAdapter and FindService
     *
     * @param LocationQuery $locationQuery
     * @param Request $request
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createLocationSearchPager(LocationQuery $locationQuery, Request $request, $maxPerPage)
    {
        $adapter = new LocationSearchAdapter($locationQuery, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $request, $maxPerPage);
    }

    /**
     * Returns Pagerfanta pager that starts from first page
     * and uses 'page' GET parameter from Request object
     * configured with
     * LocationSearchHitAdapter and FindService
     *
     * @param LocationQuery $locationQuery
     * @param Request $request
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function createLocationSearchHitPager(LocationQuery $locationQuery, Request $request, $maxPerPage)
    {
        $adapter = new LocationSearchHitAdapter($locationQuery, $this->getSite()->getFindService());

        return $this->getPagerfantaPager($adapter, $request, $maxPerPage);
    }

    /**
     * Shorthand method for creating Pagerfanta pager
     * with preconfigured Adapter
     *
     * @param AdapterInterface $adapter
     * @param Request $request
     * @param int $maxPerPage
     *
     * @return Pagerfanta
     */
    protected function getPagerfantaPager(AdapterInterface $adapter, Request $request, $maxPerPage)
    {
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage(
            $request->query->get('page', 1)
        );

        return $pager;
    }
}
