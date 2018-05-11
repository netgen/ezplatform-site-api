<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\NamedQuery;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * @group config
 */
class NamedQueryTest extends AbstractParserTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new EzPublishCoreExtension([
                new NamedQuery(),
            ]),
        ];
    }

    protected function getMinimalConfiguration()
    {
        return Yaml::parse(file_get_contents(__DIR__ . '/../../Fixtures/minimal.yml'));
    }

    public function providerForTestValid()
    {
        return [
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type',
                    ]
                ],
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'use_filter' => false,
                    ],
                ],
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'max_per_page' => 10,
                    ],
                ],
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'max_per_page' => 10,
                        'page' => 2,
                    ],
                ],
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'max_per_page' => 10,
                        'page' => 2,
                        'parameters' => [
                            'some' => 'parameters',
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
    public function testValid(array $configurationValues)
    {
        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ng_named_query' => $configurationValues,
                ],
            ],
        ]);

        // Avoid detecting risky tests
        $this->assertTrue(true);
    }

    public function providerForTestInvalid()
    {
        return [
            [
                [
                    [
                        'query_type' => 'query_type',
                    ]
                ],
                'The attribute "key" must be set',
            ],
            [
                [
                    '123abc' => [
                        'query_type' => 'query_type',
                    ]
                ],
                'Query keys must be strings conforming to a valid Twig variable names',
            ],
            [
                [
                    'some_key' => [
                        'page' => 2,
                    ]
                ],
                'The child node "query_type" at path "ezpublish.system.siteaccess_group.ng_named_query.some_key" must be configured',
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'parameters' => 'parameters',
                    ]
                ],
                'Expected array, but got string',
            ],
            [
                [
                    'query_name' => [
                        'query_type' => 'query_type_name',
                        'use_filter' => [],
                    ],
                ],
                'Expected scalar, but got array',
            ],
            [
                [
                    'query_name' => [
                        'query_type' => 'query_type_name',
                        'page' => [],
                    ],
                ],
                'Expected scalar, but got array',
            ],
        ];
    }

    /**
     * @dataProvider providerForTestInvalid
     *
     * @param array $configurationValues
     * @param string $message
     */
    public function testInvalid(array $configurationValues, $message)
    {
        $this->expectException(InvalidConfigurationException::class);
        $message = preg_quote($message, '/');
        $this->expectExceptionMessageRegExp("/{$message}/");

        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ng_named_query' => $configurationValues,
                ],
            ],
        ]);
    }

    public function providerForTestDefaultValues()
    {
        return [
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type',
                    ]
                ],
                [
                    'some_key' => [
                        'query_type' => 'query_type',
                        'use_filter' => true,
                        'max_per_page' => 25,
                        'page' => 1,
                        'parameters' => [],
                    ],
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
    public function testDefaultValues(array $configurationValues, array $expectedConfigurationValues)
    {
        $this->load([
            'system' => [
                'siteaccess_group' => [
                    'ng_named_query' => $configurationValues,
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'ng_named_query',
            $expectedConfigurationValues,
            'cro'
        );
    }
}
