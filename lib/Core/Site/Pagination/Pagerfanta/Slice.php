<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use ArrayAccess;
use ArrayIterator;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use IteratorAggregate;
use RuntimeException;

/**
 * Implements IteratorAggregate with access to the array of the SearchHit instances
 * and aggregated ArrayIterator over values contained in them.
 */
final class Slice implements IteratorAggregate, ArrayAccess
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

    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->searchHits);
    }

    public function offsetGet($offset)
    {
        return $this->searchHits[$offset]->valueObject;
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method ' . __METHOD__ . ' is not supported');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method ' . __METHOD__ . ' is not supported');
    }
}
