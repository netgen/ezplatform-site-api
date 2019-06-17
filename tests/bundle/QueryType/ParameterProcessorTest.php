<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use DateTime;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ParameterProcessorTest extends TestCase
{
    public function providerForTestProcess()
    {
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
                new DateTime('@1'),
                new DateTime('@1'),
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
                '@=content',
                'CONTENT',
            ],
            [
                '@=location',
                'LOCATION',
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
                "@=config('one', 10, 'namespace', 'scope')",
                1,
            ],
            [
                "@=config('two', 20, 'namespace', 'scope')",
                2,
            ],
            [
                "@=config('three', 30, 'namespace', 'scope')",
                30,
            ],
            [
                "@=config('four', 40)",
                4,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestProcess
     *
     * @param mixed $parameter
     * @param mixed $expectedProcessedParameter
     */
    public function testProcess($parameter, $expectedProcessedParameter)
    {
        $parameterProcessor = $this->getParameterProcessorUnderTest();
        /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $viewMock */
        $viewMock = $this->getViewMock();

        $processedParameter = $parameterProcessor->process($parameter, $viewMock);

        $this->assertEquals($expectedProcessedParameter, $processedParameter);
    }

    protected function getParameterProcessorUnderTest()
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request(['page' => 422]));

        $configResolver = $this->getConfigResolverMock();

        return new ParameterProcessor($requestStack, $configResolver);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected function getConfigResolverMock()
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

    protected function getViewMock()
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

        $viewMock->method('getSiteLocation')->willReturn('LOCATION');
        $viewMock->method('getSiteContent')->willReturn('CONTENT');

        return $viewMock;
    }
}
