<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Content;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Fetch;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;

/**
 * Fetch Content QueryType test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Fetch
 */
class FetchTest extends QueryTypeBaseTest
{
    protected function getQueryTypeName(): string
    {
        return 'SiteAPI:Content/Fetch';
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new Fetch();
    }

    protected function getSupportedParameters(): array
    {
        return [
            'content_type',
            'field',
            'is_field_empty',
            'publication_date',
            'section',
            'state',
            'sort',
            'limit',
            'offset',
        ];
    }

    public function providerForTestGetQuery(): array
    {
        return [
            [
                [],
                new Query(),
            ],
            [
                [
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published asc',
                ],
                new Query([
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content_type' => [
                        'eq' => 'article',
                    ],
                    'sort' => 'published desc',
                ],
                new Query([
                    'filter' => new ContentTypeIdentifier('article'),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'content_type' => [
                        'in' => [
                            'article',
                        ],
                    ],
                    'field' => [],
                    'sort' => [
                        'published asc',
                    ],
                ],
                new Query([
                    'filter' => new ContentTypeIdentifier(['article']),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content_type' => 'article',
                    'field' => [
                        'title' => 'Hello',
                    ],
                    'sort' => [
                        'published desc',
                        'name asc',
                    ],
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                        ]
                    ],
                    'sort' => new DatePublished(Query::SORT_DESC),
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                            'gte' => 7,
                        ]
                    ],
                    'sort' => [
                        'published desc',
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new Field('title', Operator::GTE, 7),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'publication_date' => '4 May 2018',
                    'sort' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new Query([
                    'filter' => new DateMetadata(
                        DateMetadata::CREATED,
                        Operator::EQ,
                        1525384800
                    ),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidOptions(): array
    {
        return [
            [
                [
                    'content_type' => 1,
                ],
            ],
            [
                [
                    'field' => 1,
                ],
            ],
            [
                [
                    'publication_date' => true,
                ],
            ],
            [
                [
                    'publication_date' => [false],
                ],
            ],
            [
                [
                    'limit' => 'five',
                ],
            ],
            [
                [
                    'offset' => 'ten',
                ],
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidCriteria(): array
    {
        return [
            [
                [
                    'publication_date' => [
                        'like' => 5,
                    ],
                ],
            ]
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException(): array
    {
        return [
            [
                [
                    'sort' => 'just sort it',
                ],
            ],
        ];
    }
}
