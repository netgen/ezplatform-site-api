<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\Parser\AbstractParserTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\NamedObject;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Yaml\Yaml;

/**
 * @group config
 *
 * @internal
 */
final class NamedObjectTest extends AbstractParserTestCase
{
    public function getConfigurationNames(): array
    {
        return [
            'content_items',
            'locations',
            'tags',
        ];
    }

    public function getValidConfigurationValuePairs(): array
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

    public function providerForTestValid(): \Generator
    {
        $names = $this->getConfigurationNames();
        $valuePairs = $this->getValidConfigurationValuePairs();

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
     * @dataProvider providerForTestValid
     *
     * @param string $name
     * @param array $configuration
     * @param mixed $expectedValue
     */
    public function testValid(string $name, array $configuration, $expectedValue): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_named_objects' => [
                        $name => $configuration,
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'ezsettings.ezdemo_site.ng_named_objects',
            [
                $name => $expectedValue,
            ] + [
                'content_items' => [],
                'locations' => [],
                'tags' => [],
            ]
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

    public function providerForTestInvalid(): \Generator
    {
        $names = $this->getConfigurationNames();
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
     * @dataProvider providerForTestInvalid
     *
     * @param string $name
     * @param array $configuration
     */
    public function testInvalid(string $name, array $configuration): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_named_objects' => [
                        $name => $configuration,
                    ],
                ],
            ],
        ]);
    }

    public function providerForTestDefaultValues(): array
    {
        return [
            [
                null,
                [
                    'content_items' => [],
                    'locations' => [],
                    'tags' => [],
                ],
            ],
            [
                [],
                [
                    'content_items' => [],
                    'locations' => [],
                    'tags' => [],
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
    public function testDefaultValues($configurationValues, array $expectedConfigurationValues): void
    {
        $this->load([
            'system' => [
                'ezdemo_group' => [
                    'ng_named_objects' => $configurationValues,
                ],
            ],
        ]);

        $this->assertConfigResolverParameterValue(
            'ng_named_objects',
            $expectedConfigurationValues,
            'ezdemo_site'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new EzPublishCoreExtension([
                new NamedObject(),
            ]),
        ];
    }

    protected function getMinimalConfiguration(): array
    {
        return Yaml::parse(\file_get_contents(__DIR__ . '/../../Fixtures/minimal.yml'));
    }
}
