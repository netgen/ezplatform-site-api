<?php

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Content object represents eZ Platform Repository Content object in a current version
 * and specific language.
 *
 * Corresponds to eZ Platform Repository Content and ContentInfo objects.
 * @see \eZ\Publish\API\Repository\Values\Content\Content
 * @see \eZ\Publish\API\Repository\Values\Content\ContentInfo
 *
 * @property-read string|int $id
 * @property-read int|string $contentTypeId
 * @property-read int|string $sectionId
 * @property-read int $currentVersionNo
 * @property-read bool $published
 * @property-read int|string $ownerId
 * @property-read \DateTime $modificationDate
 * @property-read \DateTime $publishedDate
 * @property-read bool $alwaysAvailable
 * @property-read string $remoteId
 * @property-read string $mainLanguageCode
 * @property-read int|string $mainLocationId
 * @property-read string $name
 * @property-read string $languageCode
 * @property-read string $contentTypeIdentifier
 * @property-read string $contentTypeName
 * @property-read string $contentTypeDescription
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Field[] $fields
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Location|null $mainLocation
 * @property-read \eZ\Publish\API\Repository\Values\Content\Content $innerContent
 * @property-read \eZ\Publish\API\Repository\Values\Content\ContentInfo $innerContentInfo
 * @property-read \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentType $innerContentType
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
}
