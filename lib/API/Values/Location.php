<?php

namespace Netgen\EzPlatformSite\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Location represents location of Site Content object in the content tree.
 *
 * Corresponds to eZ Platform Repository Location object.
 * @see \eZ\Publish\API\Repository\Values\Content\Location
 *
 * @property-read string|int $id
 * @property-read string|int $parentLocationId
 * @property-read \eZ\Publish\API\Repository\Values\Content\Location $innerLocation
 * @property-read \Netgen\EzPlatformSite\API\Values\ContentInfo $contentInfo
 */
abstract class Location extends ValueObject
{
}
