<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView;

/**
 * @group config
 */
class ContentViewTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new EzPublishCoreExtension([
                new ContentView(),
            ]),
        ];
    }

    public function providerForTestValid()
    {
        return [
            [
                [
                    'match' => ['config']
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
    public function testValid(array $configurationValues)
    {
        $this->load($configurationValues);

        // Avoid detecting risky tests
        $this->assertTrue(true);
    }

    public function providerForTestInvalid()
    {
        return [
            [
                [
                    'match' => ['config'],
                    'queries' => ['query'],
                ],
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
            ],
            [
                [
                    'match' => ['config'],
                    'queries' => [
                        'query_name' => [
                            'query_type' => 'query_type_name',
                            'max_per_page' => 'ten',
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
                            'page' => 'two',
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
                            'parameters' => 'parameters',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestInvalid
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     *
     * @param array $configurationValues
     */
    public function testInvalid(array $configurationValues)
    {
        $this->load($configurationValues);
    }

    protected function load(array $configurationValues = [])
    {
        $configurationValues = [
            'system' => [
                'siteaccess_group' => [
                    'ngcontent_view' => [
                        'some_view' => [
                            'some_key' => $configurationValues,
                        ],
                    ],
                ],
            ],
        ];

        parent::load($configurationValues);
    }
}
