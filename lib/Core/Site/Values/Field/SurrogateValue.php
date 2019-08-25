<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values\Field;

use eZ\Publish\Core\FieldType\Value;

/**
 * Used as a value of a 'ngsurrogate' Field, returned when accessing non-existent Content Field.
 *
 * For that purpose the object is made to be resistant to crashes if used as a value object
 * of another field type.
 */
class SurrogateValue extends Value
{
    /** @noinspection MagicMethodsValidityInspection */

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
    }

    public function __get($property)
    {
        return null;
    }

    public function __set($property, $value): void
    {
        // do nothing
    }

    public function __isset($property): bool
    {
        return false;
    }

    public function __unset($property): void
    {
        // do nothing
    }

    public function __call($name, $arguments)
    {
        return null;
    }

    public function __toString(): string
    {
        return '';
    }
}
