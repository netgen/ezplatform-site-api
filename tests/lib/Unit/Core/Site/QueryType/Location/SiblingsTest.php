<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Depth;
use eZ\Publish\Core\Repository\Values\Content\Location as RepositoryLocation;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location\Siblings;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use Psr\Log\NullLogger;

/**
 * Location Siblings QueryType test case.
 *
 * @group query-type
 *
 * @internal
 */
final class SiblingsTest extends QueryTypeBaseTest
{
    public function providerForTestGetQuery(): array
    {
        $location = $this->getTestLocation();

        return [
            [
                [
                    'location' => $location,
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
                    ]),
                    'sortClauses' => [
                        new Depth(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'sort' => 'published asc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'location' => $location,
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published desc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                    'priority' => [
                        'lt' => 101,
                        'gte' => 57,
                    ],
                    'sort' => [
                        'published asc',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Priority(Operator::LT, 101),
                        new Priority(Operator::GTE, 57),
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                    'modification_date' => '4 May 2018',
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
                        new DateMetadata(
                            DateMetadata::MODIFIED,
                            Operator::EQ,
                            1525384800
                        ),
                        new Field('title', Operator::EQ, 'Hello'),
                        new Field('title', Operator::GTE, 7),
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
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
                        new ParentLocationId(42),
                        new LogicalNot(new LocationId(24)),
                    ]),
                    'sortClauses' => [
                        new Depth(Query::SORT_ASC),
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
        return 'SiteAPI:Location/Siblings';
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new Siblings(new NullLogger());
    }

    protected function getTestLocation(): Location
    {
        $loadServiceMock = $this->getMockBuilder(LoadService::class)->getMock();
        $siteMock = $this->getMockBuilder(Site::class)->getMock();
        $siteMock
            ->method('getLoadService')
            ->willReturn($loadServiceMock);

        $parentLocation = new Location(
            [
                'site' => false,
                'domainObjectMapper' => false,
                'innerVersionInfo' => false,
                'languageCode' => false,
                'innerLocation' => new RepositoryLocation([
                    'id' => 42,
                    'sortField' => RepositoryLocation::SORT_FIELD_DEPTH,
                    'sortOrder' => RepositoryLocation::SORT_ORDER_ASC,
                ]),
            ],
            new NullLogger()
        );

        $loadServiceMock
            ->method('loadLocation')
            ->with(42)
            ->willReturn($parentLocation);

        return new Location(
            [
                'site' => $siteMock,
                'domainObjectMapper' => false,
                'innerVersionInfo' => false,
                'languageCode' => false,
                'innerLocation' => new RepositoryLocation([
                    'id' => 24,
                    'parentLocationId' => 42,
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
            'main',
            'priority',
            'visible',
            'location',
        ];
    }
}
