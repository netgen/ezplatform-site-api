<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentInfoSearchAdapter;

/**
 * @group pager
 */
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
