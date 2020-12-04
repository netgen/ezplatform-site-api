<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Location\Relations;

use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location\Relations\AllTagFields;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\ContentFieldsMockTrait;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Value as TagValue;
use Psr\Log\NullLogger;

/**
 * AllTagFields Location Relation QueryType test case.
 *
 * @group query-type
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location\Relations\AllTagFields
 *
 * @internal
 */
final class AllTagFieldsTest extends QueryTypeBaseTest
{
    use ContentFieldsMockTrait;

    public function providerForTestGetQuery(): array
    {
        $contentWithTags = $this->getTestContentWithTags();
        $contentWithoutTags = $this->getTestContentWithoutTags();

        return [
            [
                [
                    'content' => $contentWithTags,
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published asc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new TagId([1, 2, 3, 4]),
                        new LogicalNot(new ContentId(42)),
                    ]),
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithoutTags,
                    'content_type' => 'article',
                    'sort' => 'published desc',
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new ContentTypeIdentifier('article'),
                        new MatchNone(),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithTags,
                    'exclude_self' => true,
                    'content_type' => 'article',
                    'field' => [],
                    'sort' => [
                        'published asc',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new ContentTypeIdentifier('article'),
                        new TagId([1, 2, 3, 4]),
                        new LogicalNot(new ContentId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithTags,
                    'exclude_self' => false,
                    'content_type' => 'article',
                    'field' => [
                        'title' => 'Hello',
                    ],
                    'sort' => [
                        'published desc',
                        'name asc',
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new TagId([1, 2, 3, 4]),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithTags,
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                        ],
                    ],
                    'sort' => new DatePublished(Query::SORT_DESC),
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new TagId([1, 2, 3, 4]),
                        new LogicalNot(new ContentId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithTags,
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                            'gte' => 7,
                        ],
                    ],
                    'sort' => [
                        'published desc',
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new Field('title', Operator::GTE, 7),
                        new TagId([1, 2, 3, 4]),
                        new LogicalNot(new ContentId(42)),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $contentWithTags,
                    'creation_date' => '4 May 2018',
                    'sort' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new LocationQuery([
                    'filter' => new LogicalAnd([
                        new IsMainLocation(IsMainLocation::MAIN),
                        new Visibility(Visibility::VISIBLE),
                        new DateMetadata(
                            DateMetadata::CREATED,
                            Operator::EQ,
                            1525384800
                        ),
                        new TagId([1, 2, 3, 4]),
                        new LogicalNot(new ContentId(42)),
                    ]),
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
        $content = $this->getTestContentWithTags();

        return [
            [
                [
                    'content' => $content,
                    'content_type' => 1,
                ],
            ],
            [
                [
                    'content' => $content,
                    'field' => 1,
                ],
            ],
            [
                [
                    'content' => $content,
                    'creation_date' => true,
                ],
            ],
            [
                [
                    'content' => $content,
                    'limit' => 'five',
                ],
            ],
            [
                [
                    'content' => $content,
                    'offset' => 'ten',
                ],
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidCriteria(): array
    {
        $content = $this->getTestContentWithTags();

        return [
            [
                [
                    'content' => $content,
                    'creation_date' => [
                        'like' => 5,
                    ],
                ],
            ],
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException(): array
    {
        $content = $this->getTestContentWithTags();

        return [
            [
                [
                    'content' => $content,
                    'sort' => 'just sort it',
                ],
            ],
        ];
    }

    protected function getQueryTypeName(): string
    {
        return 'SiteAPI:Location/Relations/AllTagFields';
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new AllTagFields();
    }

    protected function internalGetRepoFields(): array
    {
        return [
            new RepoField([
                'id' => 1,
                'fieldDefIdentifier' => 'tags_a',
                'value' => new TagValue([
                    new Tag([
                        'id' => 1,
                    ]),
                    new Tag([
                        'id' => 2,
                    ]),
                ]),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'eztags',
            ]),
            new RepoField([
                'id' => 2,
                'fieldDefIdentifier' => 'tags_b',
                'value' => new TagValue([
                    new Tag([
                        'id' => 3,
                    ]),
                    new Tag([
                        'id' => 4,
                    ]),
                ]),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'eztags',
            ]),
            new RepoField([
                'id' => 3,
                'fieldDefIdentifier' => 'third',
                'value' => new TagValue([
                    new Tag([
                        'id' => 3,
                    ]),
                    new Tag([
                        'id' => 4,
                    ]),
                ]),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezstring',
            ]),
        ];
    }

    protected function internalGetRepoFieldDefinitions(): array
    {
        return [
            new FieldDefinition([
                'id' => 1,
                'identifier' => 'tags_a',
                'fieldTypeIdentifier' => 'eztags',
            ]),
            new FieldDefinition([
                'id' => 2,
                'identifier' => 'tags_b',
                'fieldTypeIdentifier' => 'eztags',
            ]),
            new FieldDefinition([
                'id' => 3,
                'identifier' => 'third',
                'fieldTypeIdentifier' => 'ezstring',
            ]),
        ];
    }

    protected function getTestContentWithTags(): Content
    {
        return new Content(
            [
                'id' => 42,
                'site' => false,
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'innerContent' => $this->getRepoContent(),
                'innerVersionInfo' => $this->getRepoVersionInfo(),
                'languageCode' => 'eng-GB',
            ],
            true,
            new NullLogger()
        );
    }

    protected function getTestContentWithoutTags(): Content
    {
        return new Content(
            [
                'id' => 42,
                'site' => false,
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'fields' => [],
                'languageCode' => 'eng-GB',
            ],
            true,
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
            'parent_location_id',
            'priority',
            'subtree',
            'visible',
            'content',
            'exclude_self',
        ];
    }
}
