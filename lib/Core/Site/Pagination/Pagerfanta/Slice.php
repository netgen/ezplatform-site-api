<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use ArrayIterator;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use IteratorAggregate;

/**
 * Implements IteratorAggregate with access to the array of the SearchHit instances
 * and aggregated ArrayIterator over values contained in them.
 */
final class Slice implements IteratorAggregate
{
    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[]
     */
    private $searchHits;

    public function __construct(array $searchHits)
    {
        $this->searchHits = $searchHits;
    }

    public function getSearchHits()
    {
        return $this->searchHits;
    }

    public function getIterator()
    {
        return new ArrayIterator(
            array_map(
                function (SearchHit $searchHit) {
                    return $searchHit->valueObject;
                },
                $this->searchHits
            )
        );
    }
}
