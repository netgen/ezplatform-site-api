<?php

declare(strict_types=1);

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
    public function build(array $definitions): array
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
     */
    private function dispatchBuild(CriterionDefinition $definition): ?Criterion
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
            case 'creation_date':
                return $this->buildDateMetadataCreated($definition);
            case 'modification_date':
                return $this->buildDateMetadataModified($definition);
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
     */
    private function buildContentTypeIdentifier(CriterionDefinition $definition): ContentTypeIdentifier
    {
        return new ContentTypeIdentifier($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildDepth(CriterionDefinition $definition): Depth
    {
        return new Depth($definition->operator, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildField(CriterionDefinition $definition): Field
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
     */
    private function buildIsMainLocation(CriterionDefinition $definition): ?IsMainLocation
    {
        if ($definition->value === null) {
            return null;
        }

        $isMainLocation = $definition->value ? IsMainLocation::MAIN : IsMainLocation::NOT_MAIN;

        return new IsMainLocation($isMainLocation);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildLogicalNot(CriterionDefinition $definition): LogicalNot
    {
        $criteria = $this->build($definition->value);
        $criterion = $this->reduceCriteria($criteria);

        return new LogicalNot($criterion);
    }

    private function reduceCriteria(array $criteria): Criterion
    {
        if (\count($criteria) === 1) {
            return \reset($criteria);
        }

        return new LogicalAnd($criteria);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildParentLocationId(CriterionDefinition $definition): ParentLocationId
    {
        return new ParentLocationId($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildPriority(CriterionDefinition $definition): Priority
    {
        return new Priority($definition->operator, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildDateMetadataCreated(CriterionDefinition $definition): DateMetadata
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
     */
    private function buildDateMetadataModified(CriterionDefinition $definition): DateMetadata
    {
        return new DateMetadata(
            DateMetadata::MODIFIED,
            $definition->operator,
            $this->resolveTimeValues($definition->value)
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildSection(CriterionDefinition $definition): SectionIdentifier
    {
        return new SectionIdentifier($definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildObjectState(CriterionDefinition $definition): ObjectStateIdentifier
    {
        return new ObjectStateIdentifier($definition->target, $definition->value);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildSubtree(CriterionDefinition $definition): Subtree
    {
        return new Subtree($definition->value);
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array|false|int
     */
    private function resolveTimeValues($valueOrValues)
    {
        if (!\is_array($valueOrValues)) {
            return $this->resolveTimeValue($valueOrValues);
        }

        $returnValues = [];

        foreach ($valueOrValues as $key => $value) {
            $returnValues[$key] = $this->resolveTimeValue($value);
        }

        return $returnValues;
    }

    /**
     * @param int|string $value
     *
     * @throws \InvalidArgumentException
     */
    private function resolveTimeValue($value): int
    {
        if (\is_int($value)) {
            return $value;
        }

        $timestamp = \strtotime($value);

        if ($timestamp === false) {
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
     */
    private function buildVisibility(CriterionDefinition $definition): ?Visibility
    {
        if ($definition->value === null) {
            return null;
        }

        $isVisible = $definition->value ? Visibility::VISIBLE : Visibility::HIDDEN;

        return new Visibility($isVisible);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition $definition
     *
     * @throws \InvalidArgumentException
     */
    private function buildIsFieldEmpty(CriterionDefinition $definition): ?IsFieldEmpty
    {
        if ($definition->value === null) {
            return null;
        }

        $value = $definition->value ? IsFieldEmpty::IS_EMPTY : IsFieldEmpty::IS_NOT_EMPTY;

        return new IsFieldEmpty($definition->target, $value);
    }
}
