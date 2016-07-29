<?php

namespace Netgen\EzPlatformSite\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Field represents a field of a Site Content object.
 *
 * Corresponds to eZ Platform Repository Field object.
 * @see \eZ\Publish\API\Repository\Values\Content\Field
 *
 * @property-read string|int $id
 * @property-read string $identifier
 * @property-read string $fieldTypeIdentifier
 * @property-read \eZ\Publish\SPI\FieldType\Value $value
 * @property-read \Netgen\EzPlatformSite\API\Values\Content $content
 * @property-read \eZ\Publish\API\Repository\Values\Content\Field $innerField
 * @property-read \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $innerFieldDefinition
 */
abstract class Field extends ValueObject
{
    abstract public function isEmpty();
}
