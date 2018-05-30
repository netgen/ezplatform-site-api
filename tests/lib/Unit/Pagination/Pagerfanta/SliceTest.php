<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\Slice;
use PHPUnit\Framework\TestCase;

/**
 * @group pager
 */
class SliceTest extends TestCase
{
    public function testIteration()
    {
        $slice = $this->getSlice();

        $this->assertCount(2, $slice);

        foreach ($slice as $searchHit) {
            $this->assertInternalType('string', $searchHit);
        }
    }

    public function testArrayAccess()
    {
        $slice = $this->getSlice();

        $this->assertEquals('one', $slice[0]);
        $this->assertEquals('two', $slice[1]);
    }

    public function testGetSearchHits()
    {
        $slice = $this->getSlice();

        $this->assertEquals(
            $this->getSearchHits(),
            $slice->getSearchHits()
        );
    }

    protected function getSearchHits()
    {
        return [
            new SearchHit(['valueObject' => 'one']),
            new SearchHit(['valueObject' => 'two']),
        ];
    }

    public function getSlice()
    {
        return new Slice($this->getSearchHits());
    }
}
