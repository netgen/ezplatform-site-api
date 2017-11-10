<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration\Traits;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Traits\PagerfantaFindTrait;

class PagerFantaFindStub
{
    use PagerfantaFindTrait;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    protected $site;

    /**
     * PagerFantaFindStub constructor.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getContentSearchPager(Query $query, $currentPage, $maxPerPage)
    {
        return $this->createContentSearchPager($query, $currentPage, $maxPerPage);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getContentSearchHitPager(Query $query, $currentPage, $maxPerPage)
    {
        return $this->createContentSearchHitPager($query, $currentPage, $maxPerPage);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $locationQuery
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getLocationSearchPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)
    {
        return $this->createLocationSearchPager($locationQuery, $currentPage, $maxPerPage);
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $locationQuery
     * @param int $currentPage
     * @param int $maxPerPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function getLocationSearchHitPager(LocationQuery $locationQuery, $currentPage, $maxPerPage)
    {
        return $this->createLocationSearchHitPager($locationQuery, $currentPage, $maxPerPage);
    }
}
