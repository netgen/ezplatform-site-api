<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase should be removed when lib drops support for PHP 5.5
 */
class TestCase extends BaseTestCase
{
    /**
     * Returns a test double for the specified class.
     *
     * @param string $originalClassName
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     *
     * @throws \PHPUnit_Framework_Exception
     */
    protected function createMock($originalClassName)
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}
