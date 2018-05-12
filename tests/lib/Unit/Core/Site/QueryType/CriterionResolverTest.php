<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionResolver;
use PHPUnit\Framework\TestCase;

/**
 * CriterionResolver test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaResolver
 */
class CriterionResolverTest extends TestCase
{
    public function providerForTestResolve()
    {
        return [
            [
                123,
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                ['abc', 456],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => ['abc', 456],
                    ]),
                ],
            ],
            [
                [
                    'eq' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'in' => ['abc', 456],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => ['abc', 456],
                    ]),
                ],
            ],
            [
                [
                    'eq' => 123,
                    'in' => ['abc', 456],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 123,
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => ['abc', 456],
                    ]),
                ],
            ],
            [
                [
                    'gt' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::GT,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'gte' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::GTE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'lt' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::LT,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'lte' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::LTE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'in' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'between' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::BETWEEN,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'like' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::LIKE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'contains' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::CONTAINS,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'not' => 123,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::EQ,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'not' => [123, 456],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::IN,
                                'value' => [123, 456],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'not' => [
                        'contains' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::CONTAINS,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'not' => [
                        'not' => [
                            'contains' => 123,
                        ],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'not',
                                'target' => null,
                                'operator' => Operator::IN,
                                'value' => [
                                    new CriterionDefinition([
                                        'name' => 'test',
                                        'target' => null,
                                        'operator' => Operator::CONTAINS,
                                        'value' => 123,
                                    ]),
                                ],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'not' => [
                        'eq' => 123,
                        'between' => [1, 7],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::EQ,
                                'value' => 123,
                            ]),
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::BETWEEN,
                                'value' => [1, 7],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'not' => [
                        'contains' => 123,
                    ],
                    'between' => [1, 7],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => null,
                                'operator' => Operator::CONTAINS,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => null,
                        'operator' => Operator::BETWEEN,
                        'value' => [1, 7],
                    ]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestResolve
     *
     * @param mixed $parameters
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[] $expectedCriterionDefinitions
     */
    public function testResolve($parameters, array $expectedCriterionDefinitions)
    {
        $criterionResolver = $this->getCriterionResolverUnderTest();

        $criterionArguments = $criterionResolver->resolve('test', $parameters);

        $this->assertEquals(
            $expectedCriterionDefinitions,
            $criterionArguments
        );
    }

    public function providerForTestResolveTargets()
    {
        return [
            [
                [
                    'title' => 'hello',
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                ],
            ],
            [
                [
                    'title' => [
                        'eq' => 'hello',
                    ]
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                ],
            ],
            [
                [
                    'subtitle' => ['there', 'world'],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'subtitle',
                        'operator' => Operator::IN,
                        'value' => ['there', 'world'],
                    ]),
                ],
            ],
            [
                [
                    'subtitle' => [
                        'in' => ['there', 'world'],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'subtitle',
                        'operator' => Operator::IN,
                        'value' => ['there', 'world'],
                    ]),
                ],
            ],
            [
                [
                    'title' => 'hello',
                    'subtitle' => ['there', 'world'],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'subtitle',
                        'operator' => Operator::IN,
                        'value' => ['there', 'world'],
                    ]),
                ],
            ],
            [
                [
                    'title' => 'hello',
                    'subtitle' => [
                        'in' => ['there', 'world'],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'subtitle',
                        'operator' => Operator::IN,
                        'value' => ['there', 'world'],
                    ]),
                ],
            ],
            [
                [
                    'title' => [
                        'eq' => 'hello',
                    ],
                    'subtitle' => [
                        'in' => ['there', 'world'],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'subtitle',
                        'operator' => Operator::IN,
                        'value' => ['there', 'world'],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'gt' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::GT,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'gte' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::GTE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'lt' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::LT,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'lte' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::LTE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'in' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::IN,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'between' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::BETWEEN,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'like' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::LIKE,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'contains' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::CONTAINS,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => 123,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::EQ,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => [123, 456],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::IN,
                                'value' => [123, 456],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => [
                            'contains' => 123,
                        ],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::CONTAINS,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => [
                            'not' => [
                                'contains' => 123,
                            ],
                        ],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'not',
                                'target' => null,
                                'operator' => Operator::IN,
                                'value' => [
                                    new CriterionDefinition([
                                        'name' => 'test',
                                        'target' => 'price',
                                        'operator' => Operator::CONTAINS,
                                        'value' => 123,
                                    ]),
                                ],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => [
                            'contains' => 123,
                            'between' => [123, 456],
                        ],
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::CONTAINS,
                                'value' => 123,
                            ]),
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::BETWEEN,
                                'value' => [123, 456],
                            ]),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'price' => [
                        'not' => [
                            'contains' => 123,
                        ],
                        'like' => 456,
                    ],
                ],
                [
                    new CriterionDefinition([
                        'name' => 'not',
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => [
                            new CriterionDefinition([
                                'name' => 'test',
                                'target' => 'price',
                                'operator' => Operator::CONTAINS,
                                'value' => 123,
                            ]),
                        ],
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'price',
                        'operator' => Operator::LIKE,
                        'value' => 456,
                    ]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestResolveTargets
     *
     * @param mixed $parameters
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[] $expectedCriterionDefinitions
     */
    public function testResolveTargets($parameters, array $expectedCriterionDefinitions)
    {
        $criterionResolver = $this->getCriterionResolverUnderTest();

        $criterionArguments = $criterionResolver->resolveTargets('test', $parameters);

        $this->assertEquals(
            $expectedCriterionDefinitions,
            $criterionArguments
        );
    }

    public function testResolveThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionResolver = $this->getCriterionResolverUnderTest();

        $criterionResolver->resolve(
            'test',
            [
                'smilje' => 'bosilje',
                'eq' => 2,
            ]
        );
    }

    public function testResolveTargetsThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionResolver = $this->getCriterionResolverUnderTest();

        $criterionResolver->resolveTargets(
            'test',
            [
                'mogoruš' => [
                    'šćenac' => 'sikavica',
                    'eq' => 2,
                ],
            ]
        );
    }

    protected $criterionResolver;

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionResolver
     */
    protected function getCriterionResolverUnderTest()
    {
        if (null === $this->criterionResolver) {
            $this->criterionResolver = new CriterionResolver();
        }

        return $this->criterionResolver;
    }
}
