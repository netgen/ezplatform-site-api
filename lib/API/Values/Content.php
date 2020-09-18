<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\FieldType\Value;
use Pagerfanta\Pagerfanta;

/**
 * Site Content object represents eZ Platform Repository Content object in a current version
 * and specific language.
 *
 * Corresponds to eZ Platform Repository Content object.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Content
 *
 * @property int $id
 * @property null|int $mainLocationId
 * @property string $name
 * @property string $languageCode
 * @property bool $isVisible
 * @property \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property \Netgen\EzPlatformSiteApi\API\Values\Field[]|\Netgen\EzPlatformSiteApi\API\Values\Fields $fields
 * @property null|\Netgen\EzPlatformSiteApi\API\Values\Location $mainLocation
 * @property null|\Netgen\EzPlatformSiteApi\API\Values\Content $owner
 * @property null|\eZ\Publish\API\Repository\Values\User\User $innerOwnerUser
 * @property \eZ\Publish\API\Repository\Values\Content\Content $innerContent
 * @property \eZ\Publish\API\Repository\Values\Content\VersionInfo $innerVersionInfo
 * @property \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
 */
abstract class Content extends ValueObject
{
    /**
     * Returns if content has the field with the given field definition $identifier.
     */
    abstract public function hasField(string $identifier): bool;

    /**
     * Return Field object for the given field definition $identifier.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getField(string $identifier): Field;

    /**
     * Returns if content has the field with the given field $id.
     *
     * @param int|string $id
     */
    abstract public function hasFieldById($id): bool;

    /**
     * Return Field object for the given field $id.
     *
     * @param int|string $id
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getFieldById($id): Field;

    /**
     * Return the first existing and non-empty field.
     *
     * If no field is found in the Content, a surrogate field will be returned.
     * If all found fields are empty, the first found field will be returned.
     *
     * @param string ...$otherIdentifiers
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getFirstNonEmptyField(string $firstIdentifier, string ...$otherIdentifiers): Field;

    /**
     * Returns a field value for the given field definition identifier.
     */
    abstract public function getFieldValue(string $identifier): Value;

    /**
     * Returns a field value for the given field $id.
     *
     * @param int|string $id
     */
    abstract public function getFieldValueById($id): Value;

    /**
     * Return an array of Locations, limited by optional $limit.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    abstract public function getLocations(int $limit = 25): array;

    /**
     * Return an array of Locations, limited by optional $maxPerPage and $currentPage.
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Locations
     */
    abstract public function filterLocations(int $maxPerPage = 25, int $currentPage = 1): Pagerfanta;

    /**
     * Return single related Content from $fieldDefinitionIdentifier field.
     *
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Content
     */
    abstract public function getFieldRelation(string $fieldDefinitionIdentifier): ?Content;

    /**
     * Return all related Content from $fieldDefinitionIdentifier.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    abstract public function getFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array;

    /**
     * Return related Content from $fieldDefinitionIdentifier field in Content with given $contentId,
     * optionally limited by a list of $contentTypeIdentifiers.
     *
     * @param string[] $contentTypeIdentifiers
     *
     * @return \Pagerfanta\Pagerfanta Pagerfanta instance iterating over Site API Content items
     */
    abstract public function filterFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1
    ): Pagerfanta;
}
