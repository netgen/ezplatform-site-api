<?php

namespace Netgen\EzPlatformSite\Tests\Unit\Pagination\Pagerfanta;

use Netgen\EzPlatformSite\Core\Site\Pagination\Pagerfanta\ContentInfoSearchAdapter;
use eZ\Publish\API\Repository\Values\Content\Query;

class ContentInfoSearchAdapterTest extends ContentInfoSearchHitAdapterTest
{
    protected function getAdapter(Query $query)
    {
        return new ContentInfoSearchAdapter($query, $this->findService);
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
