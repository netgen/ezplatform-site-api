<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Content\Relations;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\Core\Repository\Repository;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\AllTagFields;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field as SiteField;
use Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\QueryTypeBaseTest;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Value as TagValue;

/**
 * AllTagFields Content Relation QueryType test case.
 *
 * @group query-type
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations\AllTagFields
 */
class AllTagFieldsTest extends QueryTypeBaseTest
{
    protected function getQueryTypeName()
    {
        return 'SiteAPI:Content/Relations/AllTagFields';
    }

    protected function getQueryTypeUnderTest()
    {
        return new AllTagFields();
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

    protected function getTestContentWithTags()
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
                new SiteField([
                    'fieldTypeIdentifier' => 'eztags',
                    'content' => $contentStub,
                    'value' => new TagValue([
                        new Tag([
                            'id' => 1,
                        ]),
                        new Tag([
                            'id' => 2,
                        ]),
                    ]),
                ]),
                new SiteField([
                    'fieldTypeIdentifier' => 'eztags',
                    'content' => $contentStub,
                    'value' => new TagValue([
                        new Tag([
                            'id' => 3,
                        ]),
                        new Tag([
                            'id' => 4,
                        ]),
                    ]),
                ]),
                new SiteField([
                    'fieldTypeIdentifier' => 'ezstring',
                ]),
            ],
            'id' => 42,
        ]);
    }

    protected function getTestContentWithoutTags()
    {
        return new Content([
            'site' => false,
            'domainObjectMapper' => false,
            'repository' => $this->getRepositoryMock(),
            'fields' => [],
            'id' => 42,
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
            'exclude_self',
        ];
    }

    public function providerForTestGetQuery()
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
                new Query([
                    'filter' => new LogicalAnd([
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
                new Query([
                    'filter' => new LogicalAnd([
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
                new Query([
                    'filter' => new LogicalAnd([
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
                new Query([
                    'filter' => new LogicalAnd([
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
                        ]
                    ],
                    'sort' => new DatePublished(Query::SORT_DESC),
                ],
                new Query([
                    'filter' => new LogicalAnd([
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

    public function providerForTestGetQueryWithInvalidOptions()
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
                    'content_type' => [1],
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
                    'publication_date' => true,
                ],
            ],
            [
                [
                    'content' => $content,
                    'publication_date' => [false],
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

    public function providerForTestGetQueryWithInvalidCriteria()
    {
        $content = $this->getTestContentWithTags();

        return [
            [
                [
                    'content' => $content,
                    'publication_date' => [
                        'like' => 5,
                    ],
                ],
            ]
        ];
    }

    public function providerForTestInvalidSortClauseThrowsException()
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
}
