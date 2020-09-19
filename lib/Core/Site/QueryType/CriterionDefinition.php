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
 * @property string|null $target
 * @property mixed|null $operator
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
     */
    protected $value;
}
