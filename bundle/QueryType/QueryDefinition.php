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
 * @property-read string $maxPerPage Maximum results per page for Pagerfanta.
 * @property-read string $page Current page for Pagerfanta.
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

    /**
     * Maximum results per page for Pagerfanta.
     *
     * @var int
     */
    protected $maxPerPage;

    /**
     * Current page for Pagerfanta.
     *
     * @var int
     */
    protected $page;
}
