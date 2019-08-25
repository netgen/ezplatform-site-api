<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\Converter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

abstract class AbstractParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $loadServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $paramConverterMock;

    public function createConfiguration(?string $class = null, ?string $name = null): MockObject
    {
        $config = $this
            ->getMockBuilder(ParamConverter::class)
            ->setMethods(['getClass', 'getAliasName', 'getOptions', 'getName', 'allowArray', 'isOptional'])
            ->disableOriginalConstructor()
            ->getMock();

        if ($name !== null) {
            $config->expects($this->any())
                ->method('getName')
                ->willReturn($name);
        }

        if ($class !== null) {
            $config->expects($this->any())
                ->method('getClass')
                ->willReturn($class);
        }

        return $config;
    }
}
