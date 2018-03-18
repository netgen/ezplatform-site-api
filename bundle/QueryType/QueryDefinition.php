<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * todo
 *
 * @internal
 *
 * @property-read string $key
 * @property-read array $options
 */
final class QueryDefinition extends ValueObject
{
    /**
     * @var array
     */
    protected $options;
}
