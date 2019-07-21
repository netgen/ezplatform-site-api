<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use InvalidArgumentException;
use Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\IsFieldEmpty;
use Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier;
use Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier;

/**
 * @internal Do not depend on this service, it can be changed without warning.
 *
 * CriteriaBuilder builds criteria from CriterionDefinition instances.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition
 */
final class CriteriaBuilder
{
    /**
     * Build criteria for the given array of criterion $definitions.
     *
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[] $definitions
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    public function build(array $definitions)
    {
        $criteria = [];

        foreach ($definitions as $definition) {
            $criterion = $this->dispatchBuild($definition);

            if ($criterion instanceof Criterion) {
                $criteria[] = $criterion;
            }
        }

        return $criteria;
    }

    /**
     * Build criterion $name from the given criterion $definition.
     *
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return null|Criterion
     */
    private function dispatchBuild(CriterionDefinition $definition)
    {
        switch ($definition->name) {
            case 'content_type':
                return $this->buildContentTypeIdentifier($definition);
            case 'depth':
                return $this->buildDepth($definition);
            case 'field':
                return $this->buildField($definition);
            case 'main':
                return $this->buildIsMainLocation($definition);
            case 'not':
                return $this->buildLogicalNot($definition);
            case 'parent_location_id':
                return $this->buildParentLocationId($definition);
            case 'priority':
                return $this->buildPriority($definition);
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'publication_date':
                @trigger_error(
                    'publication_date is deprecated for removal in 3.0. Use creation_date instead.',
                    E_USER_DEPRECATED
                );
            case 'creation_date':
                return $this->buildDateMetadataCreated($definition);
            case 'section':
                return $this->buildSection($definition);
            case 'state':
                return $this->buildObjectState($definition);
            case 'subtree':
                return $this->buildSubtree($definition);
            case 'visible':
                return $this->buildVisibility($definition);
            case 'is_field_empty':
                return $this->buildIsFieldEmpty($definition);
        }

        throw new InvalidArgumentException(
            "Criterion named '{$definition->name}' is not handled"
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier
     */
    private function buildContentTypeIdentifier(CriterionDefinition $definition)
    {
        return new ContentTypeIdentifier($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth
     */
    private function buildDepth(CriterionDefinition $definition)
    {
        return new Depth($definition->operator, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Field
     */
    private function buildField(CriterionDefinition $definition)
    {
        return new Field(
            $definition->target,
            $definition->operator,
            $definition->value
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return null|IsMainLocation
     */
    private function buildIsMainLocation(CriterionDefinition $definition)
    {
        if (null === $definition->value) {
            return null;
        }

        $isMainLocation = $definition->value ? IsMainLocation::MAIN : IsMainLocation::NOT_MAIN;

        return new IsMainLocation($isMainLocation);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot
     */
    private function buildLogicalNot(CriterionDefinition $definition)
    {
        $criteria = $this->build($definition->value);

        if (1 === count($criteria)) {
            $criteria = reset($criteria);
        } else {
            $criteria = new LogicalAnd($criteria);
        }

        return new LogicalNot($criteria);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId
     */
    private function buildParentLocationId(CriterionDefinition $definition)
    {
        return new ParentLocationId($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Priority
     */
    private function buildPriority(CriterionDefinition $definition)
    {
        return new Priority($definition->operator, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata
     */
    private function buildDateMetadataCreated(CriterionDefinition $definition)
    {
        return new DateMetadata(
            DateMetadata::CREATED,
            $definition->operator,
            $this->resolveTimeValues($definition->value)
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\SectionIdentifier
     */
    private function buildSection(CriterionDefinition $definition)
    {
        return new SectionIdentifier($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\ObjectStateIdentifier
     */
    private function buildObjectState(CriterionDefinition $definition)
    {
        return new ObjectStateIdentifier($definition->target, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree
     */
    private function buildSubtree(CriterionDefinition $definition)
    {
        return new Subtree($definition->value);
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
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return null|Visibility
     */
    private function buildVisibility(CriterionDefinition $definition)
    {
        if (null === $definition->value) {
            return null;
        }

        $isVisible = $definition->value ? Visibility::VISIBLE : Visibility::HIDDEN;

        return new Visibility($isVisible);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     *
     * @return null|IsFieldEmpty
     */
    private function buildIsFieldEmpty(CriterionDefinition $definition)
    {
        $value = $definition->value ? IsFieldEmpty::IS_EMPTY : IsFieldEmpty::IS_NOT_EMPTY;

        return new IsFieldEmpty($definition->target, $value);
    }
}
