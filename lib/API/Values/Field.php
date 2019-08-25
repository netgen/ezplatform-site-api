<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site Field represents a field of a Site Content object.
 *
 * Corresponds to eZ Platform Repository Field object.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Field
 *
 * @property int|string $id
 * @property string $fieldDefIdentifier
 * @property \eZ\Publish\SPI\FieldType\Value $value
 * @property string $languageCode
 * @property string $fieldTypeIdentifier
 * @property string $name
 * @property string $description
 * @property \Netgen\EzPlatformSiteApi\API\Values\Content $content
 * @property \eZ\Publish\API\Repository\Values\Content\Field $innerField
 * @property \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $innerFieldDefinition
 */
abstract class Field extends ValueObject
{
    abstract public function isEmpty(): bool;

    /**
     * Returns whether the field is of 'ngsurrogate' type, returned when nonexistent field is requested from Content.
     *
     * @return bool
     */
    abstract public function isSurrogate(): bool;
}
