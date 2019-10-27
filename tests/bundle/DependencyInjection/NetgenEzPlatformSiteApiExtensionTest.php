<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection;

use Generator;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\NetgenEzPlatformSiteApiExtension;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class NetgenEzPlatformSiteApiExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\NetgenEzPlatformSiteApiExtension
     */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new NetgenEzPlatformSiteApiExtension();

        parent::setUp();

        $this->setParameter('kernel.bundles', []);
    }

    protected function getContainerExtensions(): array
    {
        return [$this->extension];
    }

    protected function getMinimalConfiguration(): array
    {
        return [];
    }

    public function getBooleanConfigurationNames(): array
    {
        return [
            'override_url_alias_view_action',
            'use_always_available_fallback',
            'fail_on_missing_fields',
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

    public function providerForTestBooleanConfigurationValid(): Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $valuePairs = $this->getBooleanConfigurationValidValuePairs();

        foreach ($names as $name) {
            foreach ($valuePairs as $valuePair) {
                yield [
                    $name,
                    $valuePair[0],
                    $valuePair[1]
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
                'frontend_group' => [
                    $name => $config,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'netgen_ez_platform_site_api.frontend_group.' . $name,
            $expectedValue
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

    public function providerForTestBooleanConfigurationInvalid(): Generator
    {
        $names = $this->getBooleanConfigurationNames();
        $values = $this->getBooleanConfigurationInvalidValues();

        foreach ($names as $name) {
            foreach ($values as $value) {
                yield [
                    $name,
                    $value
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
                'frontend_group' => [
                    $name => $config,
                ],
            ],
        ]);
    }

    public function getNamedObjectConfigurationNames(): array
    {
        return [
            'content',
            'location',
            'tag',
        ];
    }

    public function getNamedObjectConfigurationValuePairs(): array
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

    public function providerForTestNamedObjectConfiguration(): Generator
    {
        $names = $this->getNamedObjectConfigurationNames();
        $valuePairs = $this->getNamedObjectConfigurationValuePairs();

        foreach ($names as $name) {
            foreach ($valuePairs as $valuePair) {
                yield [
                    $name,
                    $valuePair[0],
                    $valuePair[1]
                ];
            }
        }
    }

    /**
     * @dataProvider providerForTestNamedObjectConfiguration
     *
     * @param string $name
     * @param array $config
     * @param mixed $expectedValue
     */
    public function testNamedObjectConfiguration(string $name, array $config, $expectedValue): void
    {
        $this->load([
            'system' => [
                'frontend_group' => [
                    'named_object' => [
                        $name => $config,
                    ],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'netgen_ez_platform_site_api.frontend_group.named_object',
            [
                $name => $expectedValue,
            ] + [
                'content' => [],
                'location' => [],
                'tag' => [],
            ]
        );
    }
}
