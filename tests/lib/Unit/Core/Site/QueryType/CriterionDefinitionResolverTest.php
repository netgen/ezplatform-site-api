<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinitionResolver;
use PHPUnit\Framework\TestCase;

/**
 * CriterionDefinitionResolver test case.
 *
 * @group query-type
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaResolver
 *
 * @internal
 */
final class CriterionDefinitionResolverTest extends TestCase
{
    protected $criterionDefinitionResolver;

    public function providerForTestResolve(): array
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
    public function testResolve($parameters, array $expectedCriterionDefinitions): void
    {
        $criterionDefinitionResolver = $this->getCriterionDefinitionResolverUnderTest();

        $criterionArguments = $criterionDefinitionResolver->resolve('test', $parameters);

        $this->assertEquals(
            $expectedCriterionDefinitions,
            $criterionArguments
        );
    }

    public function providerForTestResolveTargets(): array
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
                    ],
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
            [
                [
                    'image' => true,
                    'video' => false,
                ],
                [
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'image',
                        'operator' => Operator::EQ,
                        'value' => true,
                    ]),
                    new CriterionDefinition([
                        'name' => 'test',
                        'target' => 'video',
                        'operator' => Operator::EQ,
                        'value' => false,
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
    public function testResolveTargets($parameters, array $expectedCriterionDefinitions): void
    {
        $criterionDefinitionResolver = $this->getCriterionDefinitionResolverUnderTest();

        $criterionArguments = $criterionDefinitionResolver->resolveTargets('test', $parameters);

        $this->assertEquals(
            $expectedCriterionDefinitions,
            $criterionArguments
        );
    }

    public function testResolveThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionDefinitionResolver = $this->getCriterionDefinitionResolverUnderTest();

        $criterionDefinitionResolver->resolve(
            'test',
            [
                'smilje' => 'bosilje',
                'eq' => 2,
            ]
        );
    }

    public function testResolveTargetsThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionDefinitionResolver = $this->getCriterionDefinitionResolverUnderTest();

        $criterionDefinitionResolver->resolveTargets(
            'test',
            [
                'mogoruš' => [
                    'šćenac' => 'sikavica',
                    'eq' => 2,
                ],
            ]
        );
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinitionResolver
     */
    protected function getCriterionDefinitionResolverUnderTest(): CriterionDefinitionResolver
    {
        if ($this->criterionDefinitionResolver === null) {
            $this->criterionDefinitionResolver = new CriterionDefinitionResolver();
        }

        return $this->criterionDefinitionResolver;
    }
}
