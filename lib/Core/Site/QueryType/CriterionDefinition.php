<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Holds resolved values of parameters defining a criterion: name, target, operator and value.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinitionResolver
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriteriaBuilder
 *
 * @property string $name
 * @property null|string $target
 * @property null|mixed $operator
 * @property mixed $value
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
     * @var null|string
     */
    protected $target;

    /**
     * Optional operator.
     *
     * @var null|mixed
     */
    protected $operator;

    /**
     * Mandatory value.
     *
     * @var mixed
     */
    protected $value;
}
