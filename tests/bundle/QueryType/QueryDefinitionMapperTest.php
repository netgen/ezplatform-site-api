<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\QueryType;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\QueryType\QueryType;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use InvalidArgumentException;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionMapper;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType as SiteQueryType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class QueryDefinitionMapperTest extends TestCase
{
    public function providerForTestMap()
    {
        return [
            [
                [
                    'query_type' => 'query_type',
                    'use_filter' => true,
                    'max_per_page' => 10,
                    'page' => 1,
                    'parameters' => [
                        'some' => 'parameters',
                    ],
                ],
                new QueryDefinition([
                    'name' => 'query_type',
                    'parameters' => [
                        'some' => 'parameters',
                    ],
                    'useFilter' => true,
                    'maxPerPage' => 10,
                    'page' => 1,
                ]),
            ],
            [
                [
                    'query_type' => 'site_query_type',
                    'use_filter' => true,
                    'max_per_page' => 10,
                    'page' => 1,
                    'parameters' => [
                        'some' => 'parameters',
                        'content' => 'A',
                        'location' => 'B',
                    ],
                ],
                new QueryDefinition([
                    'name' => 'site_query_type',
                    'parameters' => [
                        'some' => 'parameters',
                        'content' => 'A',
                        'location' => 'B',
                    ],
                    'useFilter' => true,
                    'maxPerPage' => 10,
                    'page' => 1,
                ]),
            ],
            [
                [
                    'query_type' => 'site_query_type',
                    'use_filter' => true,
                    'max_per_page' => 10,
                    'page' => 1,
                    'parameters' => [
                        'some' => 'parameters',
                    ],
                ],
                new QueryDefinition([
                    'name' => 'site_query_type',
                    'parameters' => [
                        'some' => 'parameters',
                        'content' => 'content',
                        'location' => 'location',
                    ],
                    'useFilter' => true,
                    'maxPerPage' => 10,
                    'page' => 1,
                ]),
            ],
            [
                [
                    'named_query' => 'named_query',
                    'page' => 2,
                    'parameters' => [
                        'some' => 'pancakes',
                    ],
                ],
                new QueryDefinition([
                    'name' => 'query_type',
                    'parameters' => [
                        'some' => 'pancakes',
                        'chair' => 'table',
                    ],
                    'useFilter' => true,
                    'maxPerPage' => 10,
                    'page' => 2,
                ]),
            ],
            [
                [
                    'named_query' => 'named_site_query',
                    'page' => 3,
                    'parameters' => [
                        'some' => [
                            'various' => 'delicacies',
                        ],
                        'salad' => true,
                    ],
                    'use_filter' => false,
                ],
                new QueryDefinition([
                    'name' => 'site_query_type',
                    'parameters' => [
                        'some' => [
                            'various' => 'delicacies',
                        ],
                        'salad' => true,
                        'content' => 'content',
                        'location' => 'location',
                        'spoon' => 'soup',
                    ],
                    'useFilter' => false,
                    'maxPerPage' => 10,
                    'page' => 3,
                ]),
            ],
        ];
    }

    /**
     * @dataProvider providerForTestMap
     *
     * @param array $configuration
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $expectedQueryDefinition
     */
    public function testMap(array $configuration, QueryDefinition $expectedQueryDefinition)
    {
        $queryDefinitionMapper = $this->getQueryDefinitionMapperUnderTest();

        $queryDefinition = $queryDefinitionMapper->map($configuration, $this->getViewMock());

        $this->assertEquals($expectedQueryDefinition, $queryDefinition);
    }

    public function testMapNonexistentNamedQueryThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Could not find query configuration named 'bazooka'"
        );

        $queryDefinitionMapper = $this->getQueryDefinitionMapperUnderTest();

        $queryDefinitionMapper->map(
            [
                'named_query' => 'bazooka',
                'page' => 3,
                'parameters' => [
                    'some' => 'steaks',
                    'salad' => true,
                ],
                'use_filter' => false,
            ],
            $this->getViewMock()
        );
    }

    protected function getQueryDefinitionMapperUnderTest()
    {
        $queryDefinitionMapper = new QueryDefinitionMapper(
            $this->getQueryTypeRegistryMock(),
            $this->getParameterProcessor()
        );

        $queryDefinitionMapper->setNamedQueryConfiguration([
            'named_query' => [
                'query_type' => 'query_type',
                'use_filter' => true,
                'max_per_page' => 10,
                'page' => 1,
                'parameters' => [
                    'some' => [
                        'parameters' => 'and stuff',
                    ],
                    'chair' => 'table',
                ],
            ],
            'named_site_query' => [
                'query_type' => 'site_query_type',
                'use_filter' => true,
                'max_per_page' => 10,
                'page' => 1,
                'parameters' => [
                    'some' => [
                        'parameters' => 'and other stuff',
                    ],
                    'spoon' => 'soup',
                ],
            ],
        ]);

        return $queryDefinitionMapper;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    protected function getQueryTypeRegistryMock()
    {
        $queryTypeRegistryMock = $this->getMockBuilder(QueryTypeRegistry::class)->getMock();
        $queryTypeRegistryMock
            ->method('getQueryType')
            ->willReturnMap([
                ['query_type', $this->getQueryTypeMock()],
                ['site_query_type', $this->getSiteQueryTypeMock()],
            ]);

        return $queryTypeRegistryMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getQueryTypeMock()
    {
        return $this->getMockBuilder(QueryType::class)->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSiteQueryTypeMock()
    {
        $queryTypeMock = $this->getMockBuilder(SiteQueryType::class)->getMock();
        $queryTypeMock
            ->method('supportsParameter')
            ->willReturnMap([
                ['content', true],
                ['location', true],
            ]);

        return $queryTypeMock;
    }

    /**
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor
     */
    protected function getParameterProcessor()
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->getMockBuilder(RequestStack::class)->getMock();
        /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolverMock */
        $configResolverMock = $this->getMockBuilder(ConfigResolverInterface::class)->getMock();

        return new ParameterProcessor($requestStack, $configResolverMock);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    protected function getViewMock()
    {
        $viewMock = $this->getMockBuilder(ContentView::class)->getMock();

        $viewMock->method('getSiteLocation')->willReturn('location');
        $viewMock->method('getSiteContent')->willReturn('content');

        return $viewMock;
    }
}
