<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * QueryDefinition defines a search query through the QueryType configuration.
 *
 * @see \eZ\Publish\Core\QueryType\QueryType
 *
 * @property-read string $name QueryType name.
 * @property-read array $parameters An array of configured QueryType options.
 * @property-read bool $useFilter Whether to use FilterService or Find Service.
 * @property-read string $maxPerPage Maximum results per page for Pagerfanta.
 * @property-read string $page Current page for Pagerfanta.
 *
 * @internal Do not depend on this class, it can be changed without warning.
 */
final class QueryDefinition extends ValueObject
{
    /**
     * QueryType name.
     *
     * @see \eZ\Publish\Core\QueryType\QueryType::getName()
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
     * Whether to use FilterService or Find Service.
     *
     * @var bool
     */
    protected $useFilter;

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
