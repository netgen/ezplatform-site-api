<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

/**
 * Relation service provides methods for loading relations.
 */
interface RelationService
{
    /**
     * Load single related Content from $fieldDefinitionIdentifier field in the given
     * $content, optionally limited by a list of $contentTypeIdentifiers.
     */
    public function loadFieldRelation(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ): ?Content;

    /**
     * Load all related Content from $fieldDefinitionIdentifier field in the given
     * $content, optionally limited by a list of $contentTypeIdentifiers and $limit.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    public function loadFieldRelations(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        ?int $limit = null
    ): array;

    /**
     * Load single related Location from $fieldDefinitionIdentifier field in $content,
     * optionally limited by a list of $contentTypeIdentifiers.
     *
     * Note: only visible main Location of the related Content will be used.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location|null
     */
    public function loadFieldRelationLocation(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ): ?Location;

    /**
     * Load all related Locations from $fieldDefinitionIdentifier field in $content,
     * optionally limited by a list of $contentTypeIdentifiers and $limit.
     *
     * Note: only visible main Locations of the related Content will be used.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    public function loadFieldRelationLocations(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        ?int $limit = null
    ): array;
}
