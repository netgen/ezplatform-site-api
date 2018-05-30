<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use ArrayIterator;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;

/**
 * Implements ArrayIterator over SearchHit values and access to the array of
 * the given SearchHit instances.
 */
final class Slice extends ArrayIterator
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[]
     */
    private $searchHits;

    public function __construct(array $searchHits)
    {
        $this->searchHits = $searchHits;

        parent::__construct(
            array_map(
                function (SearchHit $searchHit) {
                    return $searchHit->valueObject;
                },
                $searchHits
            )
        );
    }

    public function getSearchHits()
    {
        return $this->searchHits;
    }
}
