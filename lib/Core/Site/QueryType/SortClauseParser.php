<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\ContentName;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DateModified;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\DatePublished;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Field;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Priority;
use InvalidArgumentException;

/**
 * Sort clause parser parses string representation of the SortClause
 * to return the SortClause instance.
 *
 * Supported sort clause strings:
 *
 *  - depth
 *  - depth asc
 *  - depth desc
 *  - field/article/title
 *  - field/article/title asc
 *  - field/article/title desc
 *  - modified
 *  - modified asc
 *  - modified desc
 *  - name
 *  - name asc
 *  - name desc
 *  - priority
 *  - priority asc
 *  - priority desc
 *  - published
 *  - published asc
 *  - published desc
 *
 * @internal do not depend on this service, it can be changed without warning
 */
final class SortClauseParser
{
    /**
     * Return new sort clause instance by the given $definition string.
     *
     * @param string $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause
     */
    public function parse(string $definition): SortClause
    {
        $values = \explode(' ', $definition);
        $direction = $this->getDirection($values);
        $values = \explode('/', $values[0]);
        $type = $values[0];

        switch (\strtolower($type)) {
            case 'depth':
                return new Depth($direction);
            case 'field':
                return $this->buildFieldSortClause($values, $direction);
            case 'modified':
                return new DateModified($direction);
            case 'name':
                return new ContentName($direction);
            case 'priority':
                return new Priority($direction);
            case 'published':
                return new DatePublished($direction);
        }

        throw new InvalidArgumentException(
            "Could not handle sort type '{$type}'"
        );
    }

    /**
     * Build a new Field sort clause from the given arguments.
     *
     * @param array $values
     * @param mixed $direction
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause\Field
     */
    private function buildFieldSortClause(array $values, $direction): Field
    {
        if (!\array_key_exists(1, $values)) {
            throw new InvalidArgumentException(
                'Field sort clause requires ContentType identifier'
            );
        }

        if (!\array_key_exists(2, $values)) {
            throw new InvalidArgumentException(
                'Field sort clause requires FieldDefinition identifier'
            );
        }

        return new Field($values[1], $values[2], $direction);
    }

    /**
     * Resolve direction constant value from the given array of $values.
     *
     * @param string[] $values
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function getDirection(array $values): string
    {
        $direction = 'asc';

        if (\array_key_exists(1, $values)) {
            $direction = $values[1];
        }

        switch (\strtolower($direction)) {
            case 'asc':
                return Query::SORT_ASC;
            case 'desc':
                return Query::SORT_DESC;
        }

        throw new InvalidArgumentException(
            "Could not handle sort direction '{$direction}'"
        );
    }
}
