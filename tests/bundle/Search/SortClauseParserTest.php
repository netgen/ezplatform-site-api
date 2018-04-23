<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\Search;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DateModified;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Field;
use InvalidArgumentException;
use Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser;
use PHPUnit\Framework\TestCase;

/**
 * SortClauseParser test case.
 *
 * @group sort
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser
 */
class SortClauseParserTest extends TestCase
{
    public function providerForTestParseValid()
    {
        return [
            [
                'published',
                new DatePublished(Query::SORT_ASC),
            ],
            [
                'published asc',
                new DatePublished(Query::SORT_ASC),
            ],
            [
                'published desc',
                new DatePublished(Query::SORT_DESC),
            ],
            [
                'modified',
                new DateModified(Query::SORT_ASC),
            ],
            [
                'modified asc',
                new DateModified(Query::SORT_ASC),
            ],
            [
                'modified desc',
                new DateModified(Query::SORT_DESC),
            ],
            [
                'name',
                new ContentName(Query::SORT_ASC),
            ],
            [
                'name asc',
                new ContentName(Query::SORT_ASC),
            ],
            [
                'name desc',
                new ContentName(Query::SORT_DESC),
            ],
            [
                'field/article/title',
                new Field('article', 'title', Query::SORT_ASC),
            ],
            [
                'field/article/title asc',
                new Field('article', 'title', Query::SORT_ASC),
            ],
            [
                'field/article/title desc',
                new Field('article', 'title', Query::SORT_DESC),
            ],
        ];
    }

    /**
     * @dataProvider providerForTestParseValid
     *
     * @param string $stringDefinition
     * @param \eZ\Publish\API\Repository\Values\Content\Query\SortClause $expectedSortClause
     */
    public function testParseValid($stringDefinition, $expectedSortClause)
    {
        $parser = $this->getParserUnderTest();

        $sortClause = $parser->parse($stringDefinition);

        $this->assertEquals($sortClause, $expectedSortClause);
    }

    public function providerForTestParseInvalid()
    {
        return [
            [
                'blort',
                "Could not handle sort type 'blort'",
            ],
            [
                'published argh',
                "Could not handle sort direction 'argh'",
            ],
        ];
    }

    /**
     * @dataProvider providerForTestParseInvalid
     *
     * @param string $stringDefinition
     * @param string $message
     */
    public function testParseInvalid($stringDefinition, $message)
    {
        $this->expectException(InvalidArgumentException::class);
        $message = preg_quote($message, '/');
        $this->expectExceptionMessageRegExp("/{$message}/");

        $parser = $this->getParserUnderTest();
        $parser->parse($stringDefinition);
    }

    protected function getParserUnderTest()
    {
        return new SortClauseParser();
    }
}
