<?php

namespace Netgen\EzPlatformSite\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site ContentInfo object provides meta information of the Site Content object.
 *
 * Corresponds to eZ Platform Repository ContentInfo object.
 * @see \eZ\Publish\API\Repository\Values\Content\ContentInfo
 *
 * @property-read string|int $id
 * @property-read string|int $mainLocationId
 * @property-read string $contentTypeIdentifier
 * @property-read string $name
 * @property-read string $languageCode
 * @property-read \eZ\Publish\API\Repository\Values\Content\ContentInfo $innerContentInfo
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\ContentType $innerContentType
 */
abstract class ContentInfo extends ValueObject
{
}
