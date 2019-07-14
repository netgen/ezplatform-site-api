<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FindAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Provides methods to build Pagerfanta instance using on FilterAdapter or FindAdapter.
 */
trait PagerfantaTrait
{
    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    abstract protected function getSite(): Site;

    /**
     * Return Pagerfanta instance using FilterAdapter for the given $query.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function getFilterPager(Query $query, int $currentPage, int $maxPerPage): Pagerfanta
    {
        $adapter = new FilterAdapter($query, $this->getSite()->getFilterService());
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    /**
     * Return Pagerfanta instance using FindAdapter for the given $query.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    protected function getFindPager(Query $query, int $currentPage, int $maxPerPage): Pagerfanta
    {
        $adapter = new FindAdapter($query, $this->getSite()->getFindService());
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
