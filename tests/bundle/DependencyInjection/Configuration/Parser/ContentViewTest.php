<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * @group config
 *
 * @internal
 */
class ContentViewTest extends AbstractParserTestCase
{
    public function providerForTestValid(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'template' => 'template',
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'template' => 'template',
                    'controller' => 'controller',
                    'queries' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => 'named_query',
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'use_filter' => false,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                            'page' => 2,
                        ],
                    ],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 10,
                            'page' => 2,
                            'parameters' => [
                                'some' => 'parameters',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestValid
     *
     * @param array $configurationValues
     */
    public function testValid(array $configurationValues): void
    {
        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ngcontent_view' => [
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);

        // Avoid detecting risky tests
        $this->assertTrue(true);
    }

    public function providerForTestInvalid(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                    'queries' => [0 => 'query'],
                ],
                'Query keys must be strings conforming to a valid Twig variable names',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => ['123abc' => 'query'],
                ],
                'Query keys must be strings conforming to a valid Twig variable names',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'parameters' => [
                                'some' => 'parameters',
                            ],
                        ],
                    ],
                ],
                'One of "named_query" or "query_type" must be set',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'parameters' => 'parameters',
                        ],
                    ],
                ],
                'Expected array, but got string',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'use_filter' => [],
                        ],
                    ],
                ],
                'Expected scalar, but got array',
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query_name',
                            'query_type' => 'query_type_name',
                        ],
                    ],
                ],
                'You cannot use both "named_query" and "query_type" at the same time',
            ],
        ];
    }

    /**
     * @dataProvider providerForTestInvalid
     *
     * @param array $configurationValues
     * @param string $message
     */
    public function testInvalid(array $configurationValues, string $message): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $message = preg_quote($message, '/');
        $this->expectExceptionMessageRegExp("/{$message}/");

        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ngcontent_view' => [
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function providerForTestDefaultValues(): array
    {
        return [
            [
                [
                    'match' => ['config'],
                ],
                [
                    'match' => ['config'],
                    'queries' => [],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => 'named_query',
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'max_per_page' => 50,
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'max_per_page' => 50,
                            'parameters' => [],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [
                                'some' => 'value',
                            ],
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'named_query' => 'named_query',
                            'parameters' => [
                                'some' => 'value',
                            ],
                        ],
                    ],
                    'params' => [],
                ],
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type',
                        ],
                    ],
                ],
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type',
                            'parameters' => [],
                            'use_filter' => true,
                            'max_per_page' => 25,
                            'page' => 1,
                        ],
                    ],
                    'params' => [],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestDefaultValues
     *
     * @param array $configurationValues
     * @param array $expectedConfigurationValues
     */
    public function testDefaultValues(array $configurationValues, array $expectedConfigurationValues): void
    {
        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ngcontent_view' => [
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);
        $expectedConfigurationValues = [
            'some_view' => [
                'some_key' => $expectedConfigurationValues,
            ],
        ];

        $this->assertConfigResolverParameterValue(
            'ngcontent_view',
            $expectedConfigurationValues,
            'cro'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new ContentView(),
            ]),
        ];
    }

    protected function getMinimalConfiguration()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/minimal.yml'));
    }
}
