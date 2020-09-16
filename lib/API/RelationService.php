<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API;

use Netgen\EzPlatformSiteApi\API\Values\Content;

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
}
