<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DateModified;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Field;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Priority;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\SortClauseParser;
use PHPUnit\Framework\TestCase;

/**
 * SortClauseParser test case.
 *
 * @group query-type
 * @group sort
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\SortClauseParser
 *
 * @internal
 */
final class SortClauseParserTest extends TestCase
{
    public function providerForTestParseValid(): array
    {
        return [
            [
                'depth',
                new Depth(Query::SORT_ASC),
            ],
            [
                'depth asc',
                new Depth(Query::SORT_ASC),
            ],
            [
                'depth desc',
                new Depth(Query::SORT_DESC),
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
                'priority',
                new Priority(Query::SORT_ASC),
            ],
            [
                'priority asc',
                new Priority(Query::SORT_ASC),
            ],
            [
                'priority desc',
                new Priority(Query::SORT_DESC),
            ],
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
        ];
    }

    /**
     * @dataProvider providerForTestParseValid
     *
     * @param string $stringDefinition
     * @param \eZ\Publish\API\Repository\Values\Content\Query\SortClause $expectedSortClause
     */
    public function testParseValid(string $stringDefinition, SortClause $expectedSortClause): void
    {
        $parser = $this->getParserUnderTest();

        $sortClause = $parser->parse($stringDefinition);

        $this->assertEquals($sortClause, $expectedSortClause);
    }

    public function providerForTestParseInvalid(): array
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
            [
                'field asc',
                'Field sort clause requires ContentType identifier',
            ],
            [
                'field/type asc',
                'Field sort clause requires FieldDefinition identifier',
            ],
            [
                'field/article/title argh',
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
    public function testParseInvalid(string $stringDefinition, string $message): void
    {
        $this->expectException(InvalidArgumentException::class);
        $message = \preg_quote($message, '/');
        $this->expectExceptionMessageRegExp("/{$message}/");

        $parser = $this->getParserUnderTest();
        $parser->parse($stringDefinition);
    }

    protected function getParserUnderTest(): SortClauseParser
    {
        return new SortClauseParser();
    }
}
