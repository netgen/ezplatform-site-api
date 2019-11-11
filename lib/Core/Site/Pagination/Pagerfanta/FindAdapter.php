<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSearchExtra\Core\Pagination\Pagerfanta\BaseAdapter;
use Netgen\EzPlatformSiteApi\API\FindService;

/**
 * Pagerfanta adapter performing search using FindService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FindService
 */
final class FindAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    public function __construct(Query $query, FindService $findService)
    {
        parent::__construct($query);

        $this->findService = $findService;
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
            return $this->findService->findLocations($query);
        }

        return $this->findService->findContent($query);
    }
}
