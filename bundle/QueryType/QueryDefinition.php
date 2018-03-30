<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * @internal
 *
 * QueryDefinition defines a search query through the QueryType configuration.
 *
 * @see \eZ\Publish\Core\QueryType\QueryType
 *
 * @property-read string $name QueryType name.
 * @property-read array $parameters An array of configured QueryType options.
 */
final class QueryDefinition extends ValueObject
{
    /**
     * QueryType name.
     *
     * @var string
     */
    protected $name;

    /**
     * An array of configured QueryType options.
     *
     * @var array
     */
    protected $parameters;
}
