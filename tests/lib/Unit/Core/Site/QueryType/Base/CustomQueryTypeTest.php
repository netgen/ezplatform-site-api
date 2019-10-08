<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Base;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\FullText;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\SectionId;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\SectionFacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionName;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use PHPUnit\Framework\TestCase;

/**
 * Custom QueryType test case.
 *
 * @group query-type
 *
 * @internal
 */
final class CustomQueryTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $queryType = $this->getQueryTypeUnderTest();

        $this->assertEquals(
            'Test:Custom',
            $queryType::getName()
        );
    }

    public function testGetSupportedParameters(): void
    {
        $queryType = $this->getQueryTypeUnderTest();

        $this->assertEquals(
            [
                'content_type',
                'field',
                'is_field_empty',
                'creation_date',
                'modification_date',
                'section',
                'state',
                'sort',
                'limit',
                'offset',
                'prefabrication_date',
            ],
            $queryType->getSupportedParameters()
        );
    }

    public function providerForTestGetQuery(): array
    {
        return [
            [
                [
                    'prefabrication_date' => 123,
                    'sort' => 'section',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new DateMetadata(
                            DateMetadata::MODIFIED,
                            Operator::EQ,
                            123
                        ),
                        new SectionId(42),
                    ]),
                    'query' => new FullText('one AND two OR three'),
                    'sortClauses' => [
                        new SectionIdentifier(),
                    ],
                    'facetBuilders' => [
                        new SectionFacetBuilder(),
                    ],
                ]),
            ],
            [
                [
                    'prefabrication_date' => [123, 456],
                    'sort' => [
                        'whatever',
                        'section',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new DateMetadata(
                            DateMetadata::MODIFIED,
                            Operator::IN,
                            [123, 456]
                        ),
                        new SectionId(42),
                    ]),
                    'query' => new FullText('one AND two OR three'),
                    'sortClauses' => [
                        new SectionName(),
                        new SectionIdentifier(),
                    ],
                    'facetBuilders' => [
                        new SectionFacetBuilder(),
                    ],
                ]),
            ],
            [
                [
                    'prefabrication_date' => [
                        'eq' => 123,
                        'in' => [123, 456],
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new DateMetadata(
                            DateMetadata::MODIFIED,
                            Operator::EQ,
                            123
                        ),
                        new DateMetadata(
                            DateMetadata::MODIFIED,
                            Operator::IN,
                            [123, 456]
                        ),
                        new SectionId(42),
                    ]),
                    'query' => new FullText('one AND two OR three'),
                    'facetBuilders' => [
                        new SectionFacetBuilder(),
                    ],
                ]),
            ],
        ];
    }

    /**
     * @dataProvider providerForTestGetQuery
     *
     * @param array $parameters
     * @param \eZ\Publish\API\Repository\Values\Content\Query $expectedQuery
     */
    public function testGetQuery(array $parameters, Query $expectedQuery): void
    {
        $queryType = $this->getQueryTypeUnderTest();

        $query = $queryType->getQuery($parameters);

        $this->assertEquals(
            $expectedQuery,
            $query
        );
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new CustomQueryType();
    }
}
