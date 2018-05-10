<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument;
use PHPUnit\Framework\TestCase;

/**
 * CriteriaBuilder test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
 */
class CriteriaBuilderTest extends TestCase
{
    public function providerForTestBuild()
    {
        return [
            [
                'content_type',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 'article',
                    ]),
                ],
                [
                    new ContentTypeIdentifier('article'),
                ],
            ],
            [
                'content_type',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 'article',
                    ]),
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::IN,
                        'value' => ['category', 'blog'],
                    ]),
                ],
                [
                    new ContentTypeIdentifier('article'),
                    new ContentTypeIdentifier(['category', 'blog']),
                ],
            ],
            [
                'depth',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::EQ,
                        'value' => 5,
                    ]),
                ],
                [
                    new Depth(Operator::EQ, 5),
                ],
            ],
            [
                'fields',
                [
                    new CriterionArgument([
                        'target' => 'title',
                        'operator' => Operator::EQ,
                        'value' => 'Hello',
                    ]),
                ],
                [
                    new Field('title', Operator::EQ, 'Hello'),
                ],
            ],
            [
                'main',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => true,
                    ]),
                ],
                [
                    new IsMainLocation(IsMainLocation::MAIN),
                ],
            ],
            [
                'main',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => false,
                    ]),
                ],
                [
                    new IsMainLocation(IsMainLocation::NOT_MAIN),
                ],
            ],
            [
                'main',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => null,
                    ]),
                ],
                [],
            ],
            [
                'priority',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::GTE,
                        'value' => 5,
                    ]),
                ],
                [
                    new Priority(Operator::GTE, 5),
                ],
            ],
            [
                'publication_date',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => Operator::BETWEEN,
                        'value' => [123, 456],
                    ]),
                ],
                [
                    new DateMetadata(DateMetadata::CREATED, Operator::BETWEEN, [123, 456]),
                ],
            ],
            [
                'visible',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => true,
                    ]),
                ],
                [
                    new Visibility(Visibility::VISIBLE),
                ],
            ],
            [
                'visible',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => false,
                    ]),
                ],
                [
                    new Visibility(Visibility::HIDDEN),
                ],
            ],
            [
                'visible',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => null,
                    ]),
                ],
                [],
            ],
            [
                'parent_location_id',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => 123,
                    ]),
                ],
                [
                    new ParentLocationId(123),
                ],
            ],
            [
                'parent_location_id',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => ['pepa', 456],
                    ]),
                ],
                [
                    new ParentLocationId(['pepa', 456]),
                ],
            ],
            [
                'subtree',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => '/oak/branch/',
                    ]),
                ],
                [
                    new Subtree('/oak/branch/'),
                ],
            ],
            [
                'subtree',
                [
                    new CriterionArgument([
                        'target' => null,
                        'operator' => null,
                        'value' => ['/why/not/subroot/', '/ash/'],
                    ]),
                ],
                [
                    new Subtree(['/why/not/subroot/', '/ash/']),
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerForTestBuild
     *
     * @param string $name
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument[] $arguments
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion[] $expectedCriteria
     */
    public function testBuild($name, array $arguments, array $expectedCriteria)
    {
        $criteriaBuilder = $this->getCriteriaBuilderUnderTest();

        $criteria = $criteriaBuilder->build($name, $arguments);

        $this->assertEquals(
            $expectedCriteria,
            $criteria
        );
    }

    public function testBuildUnsupportedCriterionThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Criterion named 'banana' is not handled");

        $criteriaBuilder = $this->getCriteriaBuilderUnderTest();

        $criteriaBuilder->build(
            'banana',
            [new CriterionArgument()]
        );
    }

    public function testBuildDateMetadataThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'someday' is invalid time string");

        $criteriaBuilder = $this->getCriteriaBuilderUnderTest();

        $criteriaBuilder->build(
            'publication_date',
            [
                new CriterionArgument([
                    'target' => null,
                    'operator' => null,
                    'value' => 'someday',
                ]),
            ]
        );
    }

    protected $criteriaBuilder;

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
     */
    protected function getCriteriaBuilderUnderTest()
    {
        if (null === $this->criteriaBuilder) {
            $this->criteriaBuilder = new CriteriaBuilder();
        }

        return $this->criteriaBuilder;
    }
}
