<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\FilterService;

/**
 * Pagerfanta adapter performing search using FilterService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FilterService
 */
final class FilterAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FilterService
     */
    private $filterService;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\EzPlatformSiteApi\API\FilterService $filterService
     */
    public function __construct(Query $query, FilterService $filterService)
    {
        parent::__construct($query);

        $this->filterService = $filterService;
    }

    protected function executeQuery(Query $query)
    {
        if ($query instanceof LocationQuery) {
            return $this->filterService->filterLocations($query);
        }

        return $this->filterService->filterContent($query);
    }
}
