<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use ArrayAccess;
use Countable;
use IteratorAggregate;

/**
 * Collection of Content Fields, accessible as an array with FieldDefinition identifier as Field's key.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Field
 */
abstract class Fields implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * Return whether the collection contains a field with the given $identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    abstract public function hasField(string $identifier): bool;

    /**
     * Return the field with the given $identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getField(string $identifier): Field;

    /**
     * Return whether the collection contains a field with the given $id.
     *
     * @param string|int $id
     *
     * @return bool
     */
    abstract public function hasFieldById($id): bool;

    /**
     * Return the field with the given $id.
     *
     * @param string|int $id
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    abstract public function getFieldById($id): Field;
}
