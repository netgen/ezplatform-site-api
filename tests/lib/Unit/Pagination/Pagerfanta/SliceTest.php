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

    public function testIteratorArrayAccess()
    {
        $slice = $this->getSlice();
        $iterator = $slice->getIterator();

        $this->assertEquals('one', $iterator[0]);
        $this->assertEquals('two', $iterator[1]);
    }

    public function testArrayAccessGet()
    {
        $slice = $this->getSlice();

        $this->assertEquals('one', $slice[0]);
        $this->assertEquals('two', $slice[1]);
    }

    public function testArrayAccessExists()
    {
        $slice = $this->getSlice();

        $this->assertTrue(isset($slice[0]));
        $this->assertTrue(isset($slice[1]));
        $this->assertFalse(isset($slice[2]));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testArrayAccessSet()
    {
        $slice = $this->getSlice();

        $slice[0] = 1;
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testArrayAccessUnset()
    {
        $slice = $this->getSlice();

        unset($slice[0]);
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

    protected function getSlice()
    {
        return new Slice($this->getSearchHits());
    }
}
