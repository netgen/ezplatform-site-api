<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use Netgen\EzPlatformSiteApi\Core\Site\Values\Field\NullValue;
use PHPUnit\Framework\TestCase;

/**
 * Null Field value unit tests.
 *
 * @group fields
 * @see \Netgen\EzPlatformSiteApi\Core\Site\Values\Field\NullValue
 */
class NullValueTest extends TestCase
{
    public function testConstructWithArbitraryArguments()
    {
        $value = new NullValue(1, 2, 'three');

        $this->assertInstanceOf(NullValue::class, $value);
    }

    public function testGetPropertyReturnsNull()
    {
        $value = new NullValue();

        $this->assertNull($value->property);
    }

    public function testCallMethodReturnsNull()
    {
        $value = new NullValue();

        $this->assertNull($value->method());
    }

    public function testCastToStringReturnsEmptyString()
    {
        $value = new NullValue();

        $this->assertEquals('', (string)$value);
    }

    public function testCheckingForPropertyReturnsFalse()
    {
        $value = new NullValue();

        $this->assertFalse(isset($value->property));
    }

    public function testSettingPropertyDoesNothing()
    {
        $value = new NullValue();

        $value->property = 1;

        $this->addToAssertionCount(1);
    }

    public function testUnsettingPropertyDoesNothing()
    {
        $value = new NullValue();

        unset($value->property);

        $this->addToAssertionCount(1);
    }
}
