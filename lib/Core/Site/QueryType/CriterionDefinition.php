<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Holds resolved values of parameters defining a criterion: name, target, operator and value.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinitionResolver
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
 *
 * @property-read string $name
 * @property-read string|null $target
 * @property-read mixed|null $operator
 * @property-read mixed $value
 */
final class CriterionDefinition extends ValueObject
{
    /**
     * Mandatory name, needed to build a Criterion instance in CriteriaBuilder.
     *
     * @var string
     */
    protected $name;

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
