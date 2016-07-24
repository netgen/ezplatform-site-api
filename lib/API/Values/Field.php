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
 * @property-read string $typeIdentifier
 * @property-read bool $isEmpty
 * @property-read \eZ\Publish\SPI\FieldType\Value $value
 * @property-read \eZ\Publish\API\Repository\Values\Content\Field $innerField
 */
class Field extends ValueObject
{
    /**
     * @var string|int
     */
    protected $id;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $typeIdentifier;

    /**
     * @var bool
     */
    protected $isEmpty;

    /**
     * @var \eZ\Publish\SPI\FieldType\Value
     */
    protected $value;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $innerField;
}
