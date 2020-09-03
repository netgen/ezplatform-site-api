<?php


namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\BaseOptions;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\SiteApi;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @group config
 * @group xxx
 *
 * @internal
 */
class SiteApiTest extends AbstractParserTestCase
{
    public function getBooleanConfigurationNames(): array
    {
        return [
            'site_api_is_primary_content_view',
            'fallback_to_secondary_content_view',
            'fallback_without_subrequest',
            'richtext_embed_without_subrequest',
            'use_always_available_fallback',
            'fail_on_missing_field',
            'render_missing_field_info',
        ];
    }

    public function getBooleanConfigurationValidValuePairs(): array
    {
        return [
            [
                true,
                true,
            ],
            [
                false,
                false,
            ],
        ];
    }

    public function providerForTestBooleanConfigurationValid(): \Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $valuePairs = $this->getBooleanConfigurationValidValuePairs();

        foreach ($names as $name) {
            foreach ($valuePairs as $valuePair) {
                yield [
                    $name,
                    $valuePair[0],
                    $valuePair[1],
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestBooleanConfigurationValid
     *
     * @param string $name
     * @param mixed $config
     * @param mixed $expectedValue
     */
    public function testBooleanConfigurationValid(string $name, $config, $expectedValue): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        $name => $config,
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'ng_site_api.' . $name,
            $expectedValue,
            'ezdemo_site'
        );
    }

    public function getBooleanConfigurationInvalidValues(): array
    {
        return [
            0,
            1,
            'true',
            'false',
            [],
        ];
    }

    public function providerForTestBooleanConfigurationInvalid(): \Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $values = $this->getBooleanConfigurationInvalidValues();

        foreach ($names as $name) {
            foreach ($values as $value) {
                yield [
                    $name,
                    $value,
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestBooleanConfigurationInvalid
     *
     * @param string $name
     * @param mixed $config
     */
    public function testBooleanConfigurationInvalid(string $name, $config): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        $name => $config,
                    ],
                ],
            ],
        ]);
    }

    public function getNamedObjectConfigurationNames(): array
    {
        return [
            'content_items',
            'locations',
            'tags',
        ];
    }

    public function getValidNamedObjectConfigurationValuePairs(): array
    {
        return [
            [
                [
                    'napolitanke' => 42,
                ],
                [
                    'napolitanke' => [
                        'id' => 42,
                    ],
                ],
            ],
            [
                [
                    'napolitanke' => 'qwe5678',
                ],
                [
                    'napolitanke' => [
                        'remote_id' => 'qwe5678',
                    ],
                ],
            ],
            [
                [
                    'napolitanke' => [
                        'id' => 42,
                    ],
                ],
                [
                    'napolitanke' => [
                        'id' => 42,
                    ],
                ],
            ],
            [
                [
                    'napolitanke' => [
                        'remote_id' => 'asd1234',
                    ],
                ],
                [
                    'napolitanke' => [
                        'remote_id' => 'asd1234',
                    ],
                ],
            ],
            [
                [
                    'sardine' => 12,
                    'napolitanke' => [
                        'remote_id' => 'asd1234',
                    ],
                ],
                [
                    'sardine' => [
                        'id' => 12,
                    ],
                    'napolitanke' => [
                        'remote_id' => 'asd1234',
                    ],
                ],
            ],
            [
                [
                    'sardine' => 12,
                    'napolitanke' => 'asd1234',
                ],
                [
                    'sardine' => [
                        'id' => 12,
                    ],
                    'napolitanke' => [
                        'remote_id' => 'asd1234',
                    ],
                ],
            ],
        ];
    }

    public function providerForTestNamedObjectConfigurationValid(): \Generator
    {
        $names = $this->getNamedObjectConfigurationNames();
        $valuePairs = $this->getValidNamedObjectConfigurationValuePairs();

        foreach ($names as $name) {
            foreach ($valuePairs as $valuePair) {
                yield [
                    $name,
                    $valuePair[0],
                    $valuePair[1],
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestNamedObjectConfigurationValid
     *
     * @param string $name
     * @param array $configuration
     * @param mixed $expectedValue
     */
    public function testNamedObjectConfigurationValid(string $name, array $configuration, $expectedValue): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_objects' => [
                            $name => $configuration,
                        ],
                    ],
                ],
            ],
        ]);

        $defaultValues = [
            'content_items' => [],
            'locations' => [],
            'tags' => [],
        ];

        $this->assertConfigResolverParameterValue(
            'ng_site_api.named_objects',
            [$name => $expectedValue] + $defaultValues,
            'ezdemo_site'
        );

        // todo another path
        $this->assertContainerBuilderHasParameter(
            'ezsettings.ezdemo_group.ng_site_api.named_objects',
            [$name => $expectedValue] + $defaultValues
        );
    }

    public function getNamedObjectInvalidConfigurations(): array
    {
        return [
            [
                'the-object' => 12,
            ],
            [
                'an object' => 12,
            ],
            [
                '123object' => 12,
            ],
            [
                'the:object' => 12,
            ],
            [
                'object?' => 12,
            ],
        ];
    }

    public function providerForTestNamedObjectConfigurationInvalid(): \Generator
    {
        $names = $this->getNamedObjectConfigurationNames();
        $configurations = $this->getNamedObjectInvalidConfigurations();

        foreach ($names as $name) {
            foreach ($configurations as $configuration) {
                yield [
                    $name,
                    $configuration,
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestNamedObjectConfigurationInvalid
     *
     * @param string $name
     * @param array $configuration
     */
    public function testNamedObjectConfigurationInvalid(string $name, array $configuration): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_objects' => [
                            $name => $configuration,
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function providerForTestNamedObjectDefaultValues(): array
    {
        $defaultValues = [
            'content_items' => [],
            'locations' => [],
            'tags' => [],
        ];

        return [
            [
                null,
                $defaultValues,
            ],
            [
                [],
                $defaultValues,
            ],
        ];
    }

    /**
     * @dataProvider providerForTestNamedObjectDefaultValues
     *
     * @param mixed $configurationValues
     * @param array $expectedConfigurationValues
     */
    public function testNamedObjectDefaultValues($configurationValues, array $expectedConfigurationValues): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_objects' => $configurationValues,
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'ng_site_api.named_objects',
            $expectedConfigurationValues,
            'ezdemo_site'
        );
    }

    public function providerForTestNamedQueryConfigurationValid(): array
    {
        return [
            [
                [
                    'query_type' => 'query_type',
                ],
            ],
            [
                [
                    'query_type' => 'query_type_name',
                    'use_filter' => false,
                ],
            ],
            [
                [
                    'query_type' => 'query_type_name',
                    'max_per_page' => 10,
                ],
            ],
            [
                [
                    'query_type' => 'query_type_name',
                    'max_per_page' => 10,
                    'page' => 2,
                ],
            ],
            [
                [
                    'query_type' => 'query_type_name',
                    'max_per_page' => 10,
                    'page' => 2,
                    'parameters' => [
                        'some' => 'parameters',
                    ],
                    'use_filter' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestNamedQueryConfigurationValid
     *
     * @param array $configurationValues
     */
    public function testNamedQueryConfigurationValid(array $configurationValues): void
    {
        $queryName = 'query_name';

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_queries' => [
                            $queryName => $configurationValues,
                        ],
                    ],
                ],
            ],
        ]);

        $defaultValues = [
            'use_filter' => true,
            'max_per_page' => 25,
            'page' => 1,
            'parameters' => [],
        ];

        $this->assertConfigResolverParameterValue(
            'ng_site_api.named_queries',
            [$queryName => $configurationValues + $defaultValues],
            'ezdemo_site'
        );
        // Avoid detecting risky tests
        $this->assertTrue(true);
    }

    public function providerForTestNamedQueryConfigurationInvalid(): array
    {
        return [
            [
                [
                    [
                        'query_type' => 'query_type',
                    ],
                ],
                'The attribute "key" must be set',
            ],
            [
                [
                    '123abc' => [
                        'query_type' => 'query_type',
                    ],
                ],
                'Query key must be a string conforming to a valid Twig variable name',
            ],
            [
                [
                    'some_key' => [
                        'page' => 2,
                    ],
                ],
                'The child node "query_type" at path "ezpublish.system.ezdemo_group.ng_named_queries.some_key" must be configured',
            ],
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type_name',
                        'parameters' => 'parameters',
                    ],
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
     * @dataProvider providerForTestNamedQueryConfigurationInvalid
     *
     * @param array $configurationValues
     * @param string $message
     */
    public function testNamedQueryConfigurationInvalid(array $configurationValues, string $message): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $message = \preg_quote($message, '/');
        $this->matchesRegularExpression("/{$message}/");

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_queries' => $configurationValues,
                    ],
                ],
            ],
        ]);
    }

    public function providerForTestNamedQueryConfigurationDefaultValues(): array
    {
        return [
            [
                [
                    'some_key' => [
                        'query_type' => 'query_type',
                    ],
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
     * @dataProvider providerForTestNamedQueryConfigurationDefaultValues
     *
     * @param array $configurationValues
     * @param array $expectedConfigurationValues
     */
    public function testNamedQueryConfigurationDefaultValues(array $configurationValues, array $expectedConfigurationValues): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_site_api' => [
                        'named_queries' => $configurationValues,
                    ],
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'ng_site_api.named_queries',
            $expectedConfigurationValues,
            'ezdemo_site'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new SiteApi(),
            ]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return Yaml::parse(\file_get_contents(__DIR__ . '/../../Fixtures/minimal.yml'));
    }
}
