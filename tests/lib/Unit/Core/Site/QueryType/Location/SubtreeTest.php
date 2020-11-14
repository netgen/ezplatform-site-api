<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree as SubtreeCriterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Priority;
use eZ\Publish\Core\Repository\Values\Content\Location as RepositoryLocation;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location\Subtree;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use Psr\Log\NullLogger;

/**
 * Location Subtree QueryType test case.
 *
 * @group query-type
 *
 * @internal
 */
final class SubtreeTest extends QueryTypeBaseTest
{
    public function providerForTestGetQuery(): array
    {
        $location = $this->getTestLocation();

        return [
            [
                [
                    'location' => $location,
                    'exclude_self' => true,
                    'depth' => null,
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                ]),
            ],
            [
                [
                    'location' => $location,
                    'exclude_self' => false,
                    'relative_depth' => null,
                    'limit' => null,
                    'offset' => null,
                    'sort' => 'published asc',
                ],
                new LocationQuery([
                    'filter' => new SubtreeCriterion('/3/5/7/11/'),
                    'limit' => 25,
                    'offset' => 0,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'exclude_self' => null,
                    'depth' => [
                        'in' => [2, 3, 7],
                    ],
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published desc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new Depth(Operator::IN, [2, 3, 7]),
                        new SubtreeCriterion('/3/5/7/11/'),
                    ]),
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'relative_depth' => 5,
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published desc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new Depth(Operator::EQ, 9),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => null,
                    'relative_depth' => [
                        'in' => [2, 3, 7],
                    ],
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published desc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new Depth(Operator::IN, [6, 7, 11]),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => 'article',
                    'sort' => [
                        'published asc',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => 'article',
                    'field' => [],
                    'sort' => [
                        'published desc',
                        'name asc',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => 'article',
                    'field' => [
                        'title' => 'Hello',
                    ],
                    'sort' => new DatePublished(Query::SORT_DESC),
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                        ],
                    ],
                    'sort' => [
                        'published desc',
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                            'gte' => 7,
                        ],
                    ],
                    'sort' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new Field('title', Operator::GTE, 7),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'creation_date' => '4 May 2018',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new DateMetadata(
                            DateMetadata::CREATED,
                            Operator::EQ,
                            1525384800
                        ),
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                ]),
            ],
            [
                [
                    'location' => $location,
                    'sort' => $location,
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new SubtreeCriterion('/3/5/7/11/'),
                        new LogicalNot(new LocationId(42)),
                    ]),
                    'sortClauses' => [
                        new Priority(Query::SORT_DESC),
                    ],
                ]),
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidOptions(): array
    {
        $location = $this->getTestLocation();

        return [
            [
                [
                    'location' => $location,
                    'content_type' => 1,
                ],
            ],
            [
                [
                    'location' => $location,
                    'field' => 1,
                ],
            ],
            [
                [
                    'location' => $location,
                    'creation_date' => true,
                ],
            ],
            [
                [
                    'location' => $location,
                    'limit' => 'five',
                ],
            ],
            [
                [
                    'location' => $location,
                    'offset' => 'ten',
                ],
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidCriteria(): array
    {
        $location = $this->getTestLocation();

        return [
            [
                [
                    'location' => $location,
                    'creation_date' => [
                        'like' => 5,
                    ],
                ],
            ],
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException(): array
    {
        $location = $this->getTestLocation();

        return [
            [
                [
                    'location' => $location,
                    'sort' => 'just sort it',
                ],
            ],
        ];
    }

    protected function getQueryTypeName(): string
    {
        return 'SiteAPI:Location/Subtree';
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new Subtree();
    }

    protected function getTestLocation(): Location
    {
        return new Location(
            [
                'site' => false,
                'domainObjectMapper' => false,
                'innerVersionInfo' => false,
                'languageCode' => false,
                'innerLocation' => new RepositoryLocation([
                    'id' => 42,
                    'pathString' => '/3/5/7/11/',
                    'depth' => 4,
                    'sortField' => RepositoryLocation::SORT_FIELD_PRIORITY,
                    'sortOrder' => RepositoryLocation::SORT_ORDER_DESC,
                ]),
            ],
            new NullLogger()
        );
    }

    protected function getSupportedParameters(): array
    {
        return [
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
            'depth',
            'main',
            'priority',
            'visible',
            'location',
            'exclude_self',
            'relative_depth',
        ];
    }
}
