<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgumentResolver;
use PHPUnit\Framework\TestCase;

/**
 * CriterionArgumentResolver test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaResolver
 */
class CriterionArgumentResolverTest extends TestCase
{
    public function providerForTestResolve()
    {
        return [
            [
                123,
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 123,
                    ]),
                ],
            ],
            [
                ['abc', 456],
                [
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 123,
                    ]),
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::CONTAINS,
                        'value' => 123,
                    ]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestResolve
     *
     * @param mixed $parameters
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument[] $expectedCriterionArguments
     */
    public function testResolve($parameters, array $expectedCriterionArguments)
    {
        $criterionArgumentResolver = $this->getCriterionArgumentResolverUnderTest();

        $criterionArguments = $criterionArgumentResolver->resolve($parameters);

        $this->assertEquals(
            $expectedCriterionArguments,
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'hello',
                    ]),
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
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
                    new CriterionArgument([
                        'target' => 'price',
                        'operator' => Operator::CONTAINS,
                        'value' => 123,
                    ]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestResolveTargets
     *
     * @param mixed $parameters
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument[] $expectedCriterionArguments
     */
    public function testResolveTargets($parameters, array $expectedCriterionArguments)
    {
        $criterionArgumentResolver = $this->getCriterionArgumentResolverUnderTest();

        $criterionArguments = $criterionArgumentResolver->resolveTargets($parameters);

        $this->assertEquals(
            $expectedCriterionArguments,
            $criterionArguments
        );
    }

    public function testResolveThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionArgumentResolver = $this->getCriterionArgumentResolverUnderTest();

        $criterionArgumentResolver->resolve([
            'smilje' => 'bosilje',
            'eq' => 2,
        ]);
    }

    public function testResolveTargetsThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $criterionArgumentResolver = $this->getCriterionArgumentResolverUnderTest();

        $criterionArgumentResolver->resolveTargets([
            'mogoruš' => [
                'šćenac' => 'sikavica',
                'eq' => 2,
            ],
        ]);
    }

    protected $criterionArgumentResolver;

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgumentResolver
     */
    protected function getCriterionArgumentResolverUnderTest()
    {
        if (null === $this->criterionArgumentResolver) {
            $this->criterionArgumentResolver = new CriterionArgumentResolver();
        }

        return $this->criterionArgumentResolver;
    }
}
