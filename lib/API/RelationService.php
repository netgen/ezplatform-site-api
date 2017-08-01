<?php

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Relation service provides methods for loading relations.
 */
interface RelationService
{
    /**
     * Load related Content from $fieldDefinitionIdentifier field in Content with given $contentId,
     * optionally limited by a list of $contentTypeIdentifiers.
     *
     * @param int|string $contentId
     * @param string $fieldDefinitionIdentifier
     * @param array $contentTypeIdentifiers
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    public function loadFieldRelations($contentId, $fieldDefinitionIdentifier, array $contentTypeIdentifiers = []);
}
