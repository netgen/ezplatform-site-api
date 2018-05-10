<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Holds resolved values of common criterion constructor parameters: target, operator and value.
 *
 * Note: not all of these are used by every Criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionArgumentResolver
 *
 * @property-read string|null $target
 * @property-read mixed|null $operator
 * @property-read mixed $value
 */
final class CriterionArgument extends ValueObject
{
    /**
     * Optional target.
     *
     * @var string|null
     */
    protected $target;

    /**
     * Optional operator.
     *
     * @var mixed|null
     */
    protected $operator;

    /**
     * Mandatory value.
     *
     * @var mixed
     */
    protected $value;
}
