<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * QueryDefinition defines a search query through the QueryType configuration.
 *
 * @see \eZ\Publish\Core\QueryType\QueryType
 *
 * @property string $name QueryType name.
 * @property array $parameters An array of configured QueryType options.
 * @property bool $useFilter Whether to use FilterService or Find Service.
 * @property int $maxPerPage Maximum results per page for Pagerfanta.
 * @property int $page Current page for Pagerfanta.
 *
 * @internal do not depend on this class, it can be changed without warning
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
