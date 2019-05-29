<?php

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
}
