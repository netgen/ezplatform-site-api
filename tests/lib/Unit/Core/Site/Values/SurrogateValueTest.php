<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use Netgen\EzPlatformSiteApi\Core\Site\Values\Field\SurrogateValue;
use PHPUnit\Framework\TestCase;

/**
 * Surrogate Field value unit tests.
 *
 * @group fields
 * @see \Netgen\EzPlatformSiteApi\Core\Site\Values\Field\SurrogateValue
 */
class SurrogateValueTest extends TestCase
{
    public function testConstructWithArbitraryArguments()
    {
        $value = new SurrogateValue(1, 2, 'three');

        $this->assertInstanceOf(SurrogateValue::class, $value);
    }

    public function testGetPropertyReturnsNull()
    {
        $value = new SurrogateValue();

        $this->assertNull($value->property);
    }

    public function testCallMethodReturnsNull()
    {
        $value = new SurrogateValue();

        $this->assertNull($value->method());
    }

    public function testCastToStringReturnsEmptyString()
    {
        $value = new SurrogateValue();

        $this->assertEquals('', (string)$value);
    }

    public function testCheckingForPropertyReturnsFalse()
    {
        $value = new SurrogateValue();

        $this->assertFalse(isset($value->property));
    }

    public function testSettingPropertyDoesNothing()
    {
        $value = new SurrogateValue();

        $value->property = 1;

        $this->addToAssertionCount(1);
    }

    public function testUnsettingPropertyDoesNothing()
    {
        $value = new SurrogateValue();

        unset($value->property);

        $this->addToAssertionCount(1);
    }
}
