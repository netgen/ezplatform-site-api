<?php

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Content object represents eZ Platform Repository Content object in a current version
 * and specific language.
 *
 * Corresponds to eZ Platform Repository Content object.
 * @see \eZ\Publish\API\Repository\Values\Content\Content
 *
 * @property-read string|int $id
 * @property-read int|string $mainLocationId
 * @property-read string $name
 * @property-read string $languageCode
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Field[] $fields
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Location|null $mainLocation
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Content|null $owner
 * @property-read \eZ\Publish\API\Repository\Values\User\User|null $innerOwnerUser
 * @property-read \eZ\Publish\API\Repository\Values\Content\Content $innerContent
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $innerVersionInfo
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
 */
abstract class Content extends ValueObject
{
    /**
     * Returns if content has the field with the given field definition $identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    abstract public function hasField($identifier);

    /**
     * Return Field object for the given field definition $identifier, or null if not found.
     *
     * @param string $identifier
     *
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getField($identifier);

    /**
     * Returns if content has the field with the given field $id.
     *
     * @param string|int $id
     *
     * @return bool
     */
    abstract public function hasFieldById($id);

    /**
     * Return Field object for the given field $id, or null if not found.
     *
     * @param string|int $id
     *
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getFieldById($id);

    /**
     * Returns a field value for the given field definition identifier, or null if not found.
     *
     * @param string $identifier
     *
     * @return null|\eZ\Publish\SPI\FieldType\Value
     */
    abstract public function getFieldValue($identifier);

    /**
     * Returns a field value for the given field $id, or null if not found.
     *
     * @param string $id
     *
     * @return null|\eZ\Publish\SPI\FieldType\Value
     */
    abstract public function getFieldValueById($id);

    /**
     * Return an array of Locations, limited by optional $limit.
     *
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getLocations($limit = 25);

    /**
     * Return an array of Locations, limited by optional $maxPerPage and $currentPage.
     *
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]|\Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterLocations($maxPerPage = 25, $currentPage = 1);

    /**
     * Return single related Content from $fieldDefinitionIdentifier field.
     *
     * @param string $fieldDefinitionIdentifier
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content|null
     */
    abstract public function getFieldRelation($fieldDefinitionIdentifier);

    /**
     * Return all related Content from $fieldDefinitionIdentifier.
     *
     * @param string $fieldDefinitionIdentifier
     * @param int $limit
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    abstract public function getFieldRelations($fieldDefinitionIdentifier, $limit = 25);

    /**
     * Return related Content from $fieldDefinitionIdentifier field in Content with given $contentId,
     * optionally limited by a list of $contentTypeIdentifiers.
     *
     * @param string $fieldDefinitionIdentifier
     * @param string[] $contentTypeIdentifiers
     * @param int $maxPerPage
     * @param int $currentPage
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]|\Pagerfanta\Pagerfanta
     *         Pagerfanta instance iterating over Site API Content items
     */
    abstract public function filterFieldRelations(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        $maxPerPage = 25,
        $currentPage = 1
    );
}
