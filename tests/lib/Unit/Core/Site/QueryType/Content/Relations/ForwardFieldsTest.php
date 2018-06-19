<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Content\Relations;

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
use eZ\Publish\Core\Repository\Repository;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver\Relation;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver\RelationList;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\ForwardFields;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field as SiteField;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use OutOfBoundsException;

/**
 * ForwardFields Content Relation QueryType test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\ForwardFields
 */
class ForwardFieldsTest extends QueryTypeBaseTest
{
    protected function getQueryTypeName()
    {
        return 'SiteAPI:Content/Relations/ForwardFields';
    }

    protected function getQueryTypeUnderTest()
    {
        return new ForwardFields(
            new Registry([
                'ezobjectrelation' => new Relation(),
                'ezobjectrelationlist' => new RelationList(),
            ])
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected function getRepositoryMock()
    {
        $repositoryMock = $this
            ->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->any())
            ->method('getContentService')
            ->willReturn(false);

        $repositoryMock->expects($this->any())
            ->method('getUserService')
            ->willReturn(false);

        return $repositoryMock;
    }

    protected function getTestContent()
    {
        $contentStub = new Content([
            'site' => false,
            'domainObjectMapper' => false,
            'repository' => $this->getRepositoryMock(),
            'id' => 42,
        ]);

        return new Content([
            'site' => false,
            'domainObjectMapper' => false,
            'repository' => $this->getRepositoryMock(),
            'fields' => [
                'relations_a' => new SiteField([
                    'fieldTypeIdentifier' => 'ezobjectrelationlist',
                    'fieldDefIdentifier' => 'relations_a',
                    'content' => $contentStub,
                    'value' => new RelationListValue([1, 2, 3]),
                ]),
                'relations_b' => new SiteField([
                    'fieldTypeIdentifier' => 'ezobjectrelation',
                    'fieldDefIdentifier' => 'relations_b',
                    'content' => $contentStub,
                    'value' => new RelationValue(4),
                ]),
                'not_relations' => new SiteField([
                    'fieldTypeIdentifier' => 'ezstring',
                ]),
            ],
        ]);
    }

    protected function getSupportedParameters()
    {
        return [
            'content_type',
            'field',
            'publication_date',
            'section',
            'state',
            'sort',
            'limit',
            'offset',
            'content',
            'relation_field',
        ];
    }

    public function providerForTestGetQuery()
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
                        ]
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
                    'publication_date' => '4 May 2018',
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

    public function testGetQueryWithUnsupportedField()
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

    public function testGetQueryWithNonexistentField()
    {
        $this->expectException(InvalidArgumentException::class);

        $queryType = $this->getQueryTypeUnderTest();
        $content = $this->getTestContent();

        $queryType->getQuery([
            'content' => $content,
            'relation_field' => ['relations_a', 'relations_c'],
            'content_type' => 'article',
            'sort' => 'published desc',
        ]);
    }

    public function providerForTestGetQueryWithInvalidOptions()
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
                    'content_type' => [1],
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
                    'publication_date' => true,
                ],
            ],
            [
                [
                    'content' => $content,
                    'relation_field' => 'field',
                    'publication_date' => [false],
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

    public function providerForTestGetQueryWithInvalidCriteria()
    {
        $content = $this->getTestContent();

        return [
            [
                [
                    'content' => $content,
                    'relation_field' => ['relations_a', 'relations_b'],
                    'publication_date' => [
                        'like' => 5,
                    ],
                ],
            ]
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException()
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
}
