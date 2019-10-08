<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Content\Relations;

use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\FieldType\TextLine\Value;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver\Relation;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver\RelationList;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver\Surrogate;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\ForwardFields;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\ContentFieldsMockTrait;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use OutOfBoundsException;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * ForwardFields Content Relation QueryType test case.
 *
 * @group query-type
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\ForwardFields
 *
 * @internal
 */
final class ForwardFieldsTest extends QueryTypeBaseTest
{
    use ContentFieldsMockTrait;

    public function providerForTestGetQuery(): array
    {
        $content = $this->getTestContent();

        return [
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
                    'limit' => 12,
                    'offset' => 34,
                    'sort' => 'published asc',
                ],
                new Query([
                    'filter' => new ContentId([1, 2, 3, 4]),
                    'limit' => 12,
                    'offset' => 34,
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a'],
                    'content_type' => 'article',
                    'field' => [],
                    'sort' => [
                        'published asc',
                    ],
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new ContentId([1, 2, 3]),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_b'],
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
                        new ContentId([4]),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => [],
                    'content_type' => 'article',
                    'field' => [
                        'title' => [
                            'eq' => 'Hello',
                        ],
                    ],
                    'sort' => new DatePublished(Query::SORT_DESC),
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new MatchNone(),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
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
                new Query([
                    'filter' => new LogicalAnd([
                        new ContentTypeIdentifier('article'),
                        new Field('title', Operator::EQ, 'Hello'),
                        new Field('title', Operator::GTE, 7),
                        new ContentId([1, 2, 3, 4]),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
                    'creation_date' => '4 May 2018',
                    'sort' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ],
                new Query([
                    'filter' => new LogicalAnd([
                        new DateMetadata(
                            DateMetadata::CREATED,
                            Operator::EQ,
                            1525384800
                        ),
                        new ContentId([1, 2, 3, 4]),
                    ]),
                    'sortClauses' => [
                        new DatePublished(Query::SORT_DESC),
                        new ContentName(Query::SORT_ASC),
                    ],
                ]),
            ],
        ];
    }

    public function testGetQueryWithUnsupportedField(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $queryType = $this->getQueryTypeUnderTest();
        $content = $this->getTestContent();

        $queryType->getQuery([
            'content' => $content,
            'relation_field' => ['not_relations'],
            'content_type' => 'article',
            'sort' => 'published desc',
        ]);
    }

    public function testGetQueryWithNonexistentFieldFails(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Field "relations_c" in Content #42 does not exist');

        $queryType = $this->getQueryTypeUnderTest();
        $content = $this->getTestContent();

        $queryType->getQuery([
            'content' => $content,
            'relation_field' => ['relations_a', 'relations_c'],
            'content_type' => 'article',
            'sort' => 'published desc',
        ]);
    }

    public function testGetQueryWithNonexistentFieldDoesNotFail(): void
    {
        $queryType = $this->getQueryTypeUnderTest();
        $content = $this->getTestContent(false);

        $query = $queryType->getQuery([
            'content' => $content,
            'relation_field' => ['relations_a', 'relations_c'],
            'content_type' => 'article',
            'sort' => 'published desc',
        ]);

        $this->assertEquals(
            new Query([
                'filter' => new LogicalAnd([
                    new ContentTypeIdentifier('article'),
                    new ContentId([1, 2, 3]),
                ]),
                'sortClauses' => [
                    new DatePublished(Query::SORT_DESC),
                ],
            ]),
            $query
        );
    }

    public function providerForTestGetQueryWithInvalidOptions(): array
    {
        $content = $this->getTestContent();

        return [
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'content_type' => 1,
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'field' => 1,
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'creation_date' => true,
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'limit' => 'five',
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'offset' => 'ten',
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => [1],
                ],
            ],
        ];
    }

    public function providerForTestGetQueryWithInvalidCriteria(): array
    {
        $content = $this->getTestContent();

        return [
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
                    'creation_date' => [
                        'like' => 5,
                    ],
                ],
            ],
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException(): array
    {
        $content = $this->getTestContent();

        return [
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
                    'sort' => 'just sort it',
                ],
            ],
        ];
    }

    protected function getQueryTypeName(): string
    {
        return 'SiteAPI:Content/Relations/ForwardFields';
    }

    protected function getQueryTypeUnderTest(): QueryType
    {
        return new ForwardFields(
            new Registry([
                'ezobjectrelation' => new Relation(),
                'ezobjectrelationlist' => new RelationList(),
                'ngsurrogate' => new Surrogate(),
            ])
        );
    }

    protected function internalGetRepoFields(): array
    {
        return [
            new RepoField([
                'id' => 1,
                'fieldDefIdentifier' => 'relations_a',
                'value' => new RelationListValue([1, 2, 3]),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezobjectrelationlist',
            ]),
            new RepoField([
                'id' => 2,
                'fieldDefIdentifier' => 'relations_b',
                'value' => new RelationValue(4),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezobjectrelation',
            ]),
            new RepoField([
                'id' => 3,
                'fieldDefIdentifier' => 'not_relations',
                'value' => new Value(),
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
                'identifier' => 'relations_a',
                'fieldTypeIdentifier' => 'ezobjectrelationlist',
            ]),
            new FieldDefinition([
                'id' => 2,
                'identifier' => 'relations_b',
                'fieldTypeIdentifier' => 'ezobjectrelation',
            ]),
            new FieldDefinition([
                'id' => 3,
                'identifier' => 'not_relations',
                'fieldTypeIdentifier' => 'ezstring',
            ]),
        ];
    }

    protected function getTestContent(bool $failOnMissingFields = true): Content
    {
        return new Content(
            [
                'id' => 42,
                'site' => false,
                'domainObjectMapper' => $this->getDomainObjectMapper($failOnMissingFields),
                'repository' => $this->getRepositoryMock(),
                'innerContent' => $this->getRepoContent(),
                'innerVersionInfo' => $this->getRepoVersionInfo(),
                'languageCode' => 'eng-GB',
            ],
            $failOnMissingFields,
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
            'content',
            'relation_field',
        ];
    }
}
