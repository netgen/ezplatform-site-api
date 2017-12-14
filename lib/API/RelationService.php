<?php

namespace Netgen\EzPlatformSiteApi\API;

/**
 * Relation service provides methods for loading relations.
 */
interface RelationService
{
    /**
     * Load single related Content from $fieldDefinitionIdentifier field in Content with given
     * $contentId, optionally limited by a list of $contentTypeIdentifiers.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue If the $fieldDefinitionIdentifier does
     *         not exist in the Content with given $contentId
     *
     * @param $contentId
     * @param $fieldDefinitionIdentifier
     * @param array $contentTypeIdentifiers
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content|null
     */
    public function loadFieldRelation(
        $contentId,
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    );

    /**
     * Load all related Content from $fieldDefinitionIdentifier field in Content with given
     * $contentId, optionally limited by a list of $contentTypeIdentifiers and $limit.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue If the $fieldDefinitionIdentifier does
     *         not exist in the Content with given $contentId
     *
     * @param int|string $contentId
     * @param string $fieldDefinitionIdentifier
     * @param array $contentTypeIdentifiers
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    public function loadFieldRelations(
        $contentId,
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    );
}
