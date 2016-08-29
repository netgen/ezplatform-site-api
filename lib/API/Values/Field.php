<?php

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Field represents a field of a Site Content object.
 *
 * Corresponds to eZ Platform Repository Field object.
 * @see \eZ\Publish\API\Repository\Values\Content\Field
 *
 * @property-read string|int $id
 * @property-read string $fieldDefIdentifier
 * @property-read \eZ\Publish\SPI\FieldType\Value $value
 * @property-read string $languageCode
 * @property-read string $fieldTypeIdentifier
 * @property-read string $name
 * @property-read string $description
 * @property-read \Netgen\EzPlatformSiteApi\API\Values\Content $content
 * @property-read \eZ\Publish\API\Repository\Values\Content\Field $innerField
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $innerFieldDefinition
 */
abstract class Field extends ValueObject
{
    abstract public function isEmpty();
}
