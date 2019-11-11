<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSearchExtra\Core\Pagination\Pagerfanta\BaseAdapter;
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

    public function __construct(Query $query, FilterService $filterService)
    {
        parent::__construct($query);

        $this->filterService = $filterService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    protected function executeQuery(Query $query): SearchResult
    {
        if ($query instanceof LocationQuery) {
            return $this->filterService->filterLocations($query);
        }

        return $this->filterService->filterContent($query);
    }
}
