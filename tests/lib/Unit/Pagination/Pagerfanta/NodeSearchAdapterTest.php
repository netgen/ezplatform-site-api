<?php

namespace Netgen\EzPlatformSite\Tests\Unit\Pagination\Pagerfanta;

use Netgen\EzPlatformSite\Core\Site\Pagination\Pagerfanta\NodeSearchAdapter;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;

class NodeSearchAdapterTest extends NodeSearchHitAdapterTest
{
    protected function getAdapter(LocationQuery $query)
    {
        return new NodeSearchAdapter($query, $this->findService);
    }

    protected function getExpectedSlice($hits)
    {
        $expectedResult = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Search\SearchHit[] $hits */
        foreach ($hits as $hit) {
            $expectedResult[] = $hit->valueObject;
        }

        return $expectedResult;
    }
}
