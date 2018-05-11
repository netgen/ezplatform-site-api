<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use InvalidArgumentException;

/**
 * @internal Do not depend on this service, it can be changed without warning.
 *
 * CriteriaBuilder builds criteria from the query type options.
 */
final class CriteriaBuilder
{
    /**
     * Build criteria for the given configuration $name and $value.
     *
     * @param string $name
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument[] $arguments
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    public function build($name, array $arguments)
    {
        $criteria = [];

        foreach ($arguments as $argument) {
            $criterion = $this->dispatchBuild($name, $argument);

            if ($criterion instanceof Criterion) {
                $criteria[] = $criterion;
            }
        }

        return $criteria;
    }

    /**
     * Build criterion $name from the given criterion $argument.
     *
     * @param string $name
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function dispatchBuild($name, CriterionArgument $argument)
    {
        switch ($name) {
            case 'content_type':
                return $this->buildContentTypeIdentifier($argument);
            case 'depth':
                return $this->buildDepth($argument);
            case 'field':
                return $this->buildField($argument);
            case 'main':
                return $this->buildIsMainLocation($argument);
            case 'parent_location_id':
                return $this->buildParentLocationId($argument);
            case 'priority':
                return $this->buildPriority($argument);
            case 'publication_date':
                return $this->buildDateMetadataCreated($argument);
            case 'subtree':
                return $this->buildSubtree($argument);
            case 'visible':
                return $this->buildVisibility($argument);
        }

        throw new InvalidArgumentException(
            "Criterion named '{$name}' is not handled"
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier
     */
    private function buildContentTypeIdentifier(CriterionArgument $argument)
    {
        return new ContentTypeIdentifier($argument->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth
     */
    private function buildDepth(CriterionArgument $argument)
    {
        return new Depth($argument->operator, $argument->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field
     */
    private function buildField(CriterionArgument $argument)
    {
        return new Field(
            $argument->target,
            $argument->operator,
            $argument->value
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation
     */
    private function buildIsMainLocation(CriterionArgument $argument)
    {
        if (null === $argument->value) {
            return null;
        }

        $isMainLocation = $argument->value ? IsMainLocation::MAIN : IsMainLocation::NOT_MAIN;

        return new IsMainLocation($isMainLocation);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId
     */
    private function buildParentLocationId(CriterionArgument $argument)
    {
        return new ParentLocationId($argument->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority
     */
    private function buildPriority(CriterionArgument $argument)
    {
        return new Priority($argument->operator, $argument->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata
     */
    private function buildDateMetadataCreated(CriterionArgument $argument)
    {
        return new DateMetadata(
            DateMetadata::CREATED,
            $argument->operator,
            $this->resolveTimeValues($argument->value)
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree
     */
    private function buildSubtree(CriterionArgument $argument)
    {
        return new Subtree($argument->value);
    }

    /**
     * @param $valueOrValues
     *
     * @throws \InvalidArgumentException
     *
     * @return array|false|int
     */
    private function resolveTimeValues($valueOrValues)
    {
        if (!is_array($valueOrValues)) {
            return $this->resolveTimeValue($valueOrValues);
        }

        $returnValues = [];

        foreach ($valueOrValues as $key => $value) {
            $returnValues[$key] = $this->resolveTimeValue($value);
        }

        return $returnValues;
    }

    /**
     * @param $value
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    private function resolveTimeValue($value)
    {
        if (is_int($value)) {
            return $value;
        }

        $timestamp = strtotime($value);

        if (false === $timestamp) {
            throw new InvalidArgumentException(
                "'{$value}' is invalid time string"
            );
        }

        return $timestamp;
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgument $argument
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility
     */
    private function buildVisibility(CriterionArgument $argument)
    {
        if (null === $argument->value) {
            return null;
        }

        $isVisible = $argument->value ? Visibility::VISIBLE : Visibility::HIDDEN;

        return new Visibility($isVisible);
    }
}
