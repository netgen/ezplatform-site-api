<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\Converter;

use Netgen\Bundle\EzPlatformSiteApiBundle\Converter\LocationParamConverter;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class LocationParamConverterTest extends AbstractParamConverterTest
{
    const PROPERTY_NAME = 'locationId';
    const LOCATION_CLASS = Location::class;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\Converter\LocationParamConverter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->loadServiceMock = $this->createMock(LoadService::class);
        $this->converter = new LocationParamConverter($this->loadServiceMock);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(self::LOCATION_CLASS);
        $this->assertTrue($this->converter->supports($config));
    }

    public function testDoesNotSupport(): void
    {
        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));
        $config = $this->createConfiguration();
        $this->assertFalse($this->converter->supports($config));
    }

    public function testApplyLocation(): void
    {
        $id = 42;
        $valueObject = $this->createMock(Location::class);
        $this->loadServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($id)
            ->willReturn($valueObject);

        $request = new Request([], [], [self::PROPERTY_NAME => $id]);

        $config = $this->createConfiguration(self::LOCATION_CLASS, 'location');

        $this->converter->apply($request, $config);

        $this->assertInstanceOf(self::LOCATION_CLASS, $request->attributes->get('location'));
    }

    public function testApplyLocationOptionalWithEmptyAttribute(): void
    {
        $request = new Request([], [], [self::PROPERTY_NAME => null]);

        $config = $this->createConfiguration(self::LOCATION_CLASS, 'location');
        $config->expects($this->once())
            ->method('isOptional')
            ->willReturn(true);

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get('location'));
    }
}
