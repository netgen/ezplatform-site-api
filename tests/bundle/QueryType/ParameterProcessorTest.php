<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use DateTime;
use eZ\Publish\Core\FieldType\Integer\Value;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 */
final class ParameterProcessorTest extends TestCase
{
    public function providerForTestProcess(): array
    {
        $date = new DateTime('@1');

        return [
            [
                null,
                null,
            ],
            [
                true,
                true,
            ],
            [
                false,
                false,
            ],
            [
                'string',
                'string',
            ],
            [
                24,
                24,
            ],
            [
                42.24,
                42.24,
            ],
            [
                [123],
                [123],
            ],
            [
                $date,
                $date,
            ],
            [
                "@=request.query.get('page')",
                422,
            ],
            [
                "@=queryParam('page', 1)",
                422,
            ],
            [
                "@=queryParam('sage', 1)",
                1,
            ],
            [
                "@=view.hasParameter('paramExists')",
                true,
            ],
            [
                "@=view.getParameter('paramExists')",
                123,
            ],
            [
                "@=viewParam('paramExists', 1)",
                123,
            ],
            [
                "@=view.hasParameter('paramDoesNotExists')",
                false,
            ],
            [
                "@=viewParam('paramDoesNotExists', 1)",
                1,
            ],
            [
                "@=timestamp('10 September 2000 +5 days')",
                968968800,
            ],
            [
                "@=configResolver.getParameter('one', 'namespace', 'scope')",
                1,
            ],
            [
                "@=configResolver.getParameter('two', 'namespace', 'scope')",
                2,
            ],
            [
                "@=configResolver.getParameter('four')",
                4,
            ],
            [
                "@=configResolver.hasParameter('one', 'namespace', 'scope')",
                true,
            ],
            [
                "@=configResolver.hasParameter('two', 'namespace', 'scope')",
                true,
            ],
            [
                "@=configResolver.hasParameter('three', 'namespace', 'scope')",
                false,
            ],
            [
                "@=config('one', 'namespace', 'scope')",
                1,
            ],
            [
                "@=config('two', 'namespace', 'scope')",
                2,
            ],
            [
                "@=config('four')",
                4,
            ],
            [
                "@=queryParamInt('integerStringValue', 5)",
                10,
            ],
            [
                "@=queryParamInt('nonExistent', 5)",
                5,
            ],
            [
                "@=queryParamBool('booleanStringValue', false)",
                true,
            ],
            [
                "@=queryParamBool('booleanStringValue2', true)",
                false,
            ],
            [
                "@=queryParamBool('nonExistent', true)",
                true,
            ],
            [
                "@=queryParamFloat('floatStringValue', 7.7)",
                5.7,
            ],
            [
                "@=queryParamFloat('nonExistent', 7.7)",
                7.7,
            ],
            [
                "@=queryParamString('stringValue', 'yarn')",
                'strand',
            ],
            [
                "@=queryParamString('nonExistent', 'yarn')",
                'yarn',
            ],
            [
                "@=queryParam('page', 10, [10, 25, 50])",
                10,
            ],
            [
                "@=queryParam('page', '11', [10, 25, 50])",
                '11',
            ],
            [
                "@=queryParam('twentyFive', 10, [10, 25, 50])",
                25,
            ],
            [
                "@=queryParamInt('integerStringValue', 25, [10, 25, 50])",
                10,
            ],
            [
                "@=queryParamInt('integerStringValue', 11, [25, 50])",
                11,
            ],
            [
                "@=queryParamInt('integerStringValue', 10, [10, 50])",
                10,
            ],
            [
                "@=queryParamBool('booleanStringValue', false, [true, false])",
                true,
            ],
            [
                "@=queryParamBool('booleanStringValue', true, [false])",
                true,
            ],
            [
                "@=queryParamBool('booleanStringValue', true, [false])",
                true,
            ],
            [
                "@=queryParamFloat('floatStringValue', 7.7, [5.7, 7.8])",
                5.7,
            ],
            [
                "@=queryParamFloat('floatStringValue', 7.7, [5.6, 7.8])",
                7.7,
            ],
            [
                "@=queryParamFloat('floatStringValue', 3, [5.6, 7.8])",
                3.0,
            ],
            [
                "@=queryParamString('stringValue', 'and', ['hand'])",
                'and',
            ],
            [
                "@=queryParamString('stringValue', '5', ['hand', 'bland'])",
                '5',
            ],
            [
                "@=queryParamString('stringValue', 'and', ['hand', 'strand'])",
                'strand',
            ],
            [
                "@=namedContent('pterodaktilivojka')",
                $this->getContentMock(),
            ],
            [
                "@=namedLocation('grozdana')",
                $this->getLocationMock(),
            ],
            [
                "@=namedTag('radoslava')",
                $this->getTagMock(),
            ],
            [
                "@=split('pterodaktilivojka, grozdana,radoslava')",
                ['pterodaktilivojka', 'grozdana', 'radoslava'],
            ],
            [
                "@=split('burek, kifla,sirnica', ',')",
                ['burek', 'kifla', 'sirnica'],
            ],
            [
                "@=split('  marmelada ::pekmez : đem:', ':')",
                ['marmelada', 'pekmez', 'đem'],
            ],
            [
                "@=fieldValue('buhtla').value",
                5,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestProcess
     *
     * @param mixed $parameter
     * @param mixed $expectedProcessedParameter
     */
    public function testProcess($parameter, $expectedProcessedParameter): void
    {
        $parameterProcessor = $this->getParameterProcessorUnderTest();
        $viewMock = $this->getViewMock();

        $processedParameter = $parameterProcessor->process($parameter, $viewMock);

        $this->assertSame($expectedProcessedParameter, $processedParameter);
    }

    public function testProcessLanguageExpressionValues(): void
    {
        $parameterProcessor = $this->getParameterProcessorUnderTest();
        $viewMock = $this->getViewMock();

        $this->assertSame($viewMock, $parameterProcessor->process('@=view', $viewMock));
        $this->assertInstanceOf(Location::class, $parameterProcessor->process('@=location', $viewMock));
        $this->assertInstanceOf(Content::class, $parameterProcessor->process('@=content', $viewMock));
        $this->assertInstanceOf(Request::class, $parameterProcessor->process('@=request', $viewMock));
        $this->assertInstanceOf(ConfigResolverInterface::class, $parameterProcessor->process('@=configResolver', $viewMock));
    }

    protected function getParameterProcessorUnderTest(): ParameterProcessor
    {
        $requestStack = new RequestStack();
        $requestStack->push(
            new Request([
                'page' => 422,
                'twentyFive' => 25,
                'integerStringValue' => '10',
                'booleanStringValue' => 'true',
                'booleanStringValue2' => '0',
                'floatStringValue' => '5.7',
                'stringValue' => 'strand',
            ])
        );

        $configResolver = $this->getConfigResolverMock();
        $namedObjectProvider = $this->getNamedObjectProviderMock();

        return new ParameterProcessor($requestStack, $configResolver, $namedObjectProvider);
    }

    /**
     * @return \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getConfigResolverMock(): MockObject
    {
        $configResolverMock = $this->getMockBuilder(ConfigResolverInterface::class)->getMock();

        $getParameterMap = [
            ['one', 'namespace', 'scope', 1],
            ['two', 'namespace', 'scope', 2],
            ['four', null, null, 4],
        ];

        $configResolverMock
            ->method('getParameter')
            ->willReturnMap($getParameterMap);

        $hasParameterMap = [
            ['one', 'namespace', 'scope', true],
            ['two', 'namespace', 'scope', true],
            ['three', 'namespace', 'scope', false],
            ['four', null, null, true],
        ];

        $configResolverMock
            ->method('hasParameter')
            ->willReturnMap($hasParameterMap);

        return $configResolverMock;
    }

    /**
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getNamedObjectProviderMock(): MockObject
    {
        $namedObjectProviderMock = $this->getMockBuilder(Provider::class)->getMock();

        $getContentMap = [
            ['pterodaktilivojka', $this->getContentMock()],
        ];

        $namedObjectProviderMock
            ->method('getContent')
            ->willReturnMap($getContentMap);

        $getLocationMap = [
            ['grozdana', $this->getLocationMock()],
        ];

        $namedObjectProviderMock
            ->method('getLocation')
            ->willReturnMap($getLocationMap);

        $getTagMap = [
            ['radoslava', $this->getTagMock()],
        ];

        $namedObjectProviderMock
            ->method('getTag')
            ->willReturnMap($getTagMap);

        return $namedObjectProviderMock;
    }

    /**
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getViewMock(): MockObject
    {
        $viewMock = $this->getMockBuilder(ContentView::class)->getMock();

        $viewMock
            ->method('hasParameter')
            ->willReturnMap([
                ['paramExists', true],
                ['paramDoesNotExists', false],
            ]);

        $viewMock
            ->method('getParameter')
            ->willReturnMap([
                ['paramExists', 123],
            ]);

        $locationMock = $this->getLocationMock();
        $contentMock = $this->getContentMock();

        $viewMock->method('getSiteLocation')->willReturn($locationMock);
        $viewMock->method('getSiteContent')->willReturn($contentMock);

        return $viewMock;
    }

    protected function getContentMock(): MockObject
    {
        static $contentMock;

        if ($contentMock === null) {
            $contentMock = $this->getMockBuilder(Content::class)->getMock();

            $contentMock->method('getFieldValue')->willReturn(new Value(5));
        }

        return $contentMock;
    }

    protected function getLocationMock(): MockObject
    {
        static $locationMock;

        if ($locationMock === null) {
            $locationMock = $this->getMockBuilder(Location::class)->getMock();
        }

        return $locationMock;
    }

    protected function getTagMock(): MockObject
    {
        static $tagMock;

        if ($tagMock === null) {
            $tagMock = $this->getMockBuilder(Tag::class)->getMock();
        }

        return $tagMock;
    }
}
