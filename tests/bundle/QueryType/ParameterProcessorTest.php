<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

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
                new \DateTime('@1'),
                new \DateTime('@1'),
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
                '968968800',
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

        return new ParameterProcessor($requestStack);
    }

    protected function getViewMock()
    {
        $viewMock = $this->getMockBuilder(ContentView::class)->getMock();
        $viewMock
            ->expects($this->any())
            ->method('hasParameter')
            ->willReturnMap([
                ['paramExists', true],
                ['paramDoesNotExists', false],
            ]);
        $viewMock
            ->expects($this->any())
            ->method('getParameter')
            ->willReturnMap([
                ['paramExists', 123],
            ]);
        $viewMock
            ->expects($this->any())
            ->method('getSiteLocation')
            ->willReturn('LOCATION');
        $viewMock
            ->expects($this->any())
            ->method('getSiteContent')
            ->willReturn('CONTENT');

        return $viewMock;
    }
}
