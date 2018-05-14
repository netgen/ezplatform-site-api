<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use InvalidArgumentException;

/**
 * @internal Do not depend on this service, it can be changed without warning.
 *
 * CriterionResolver resolves CriterionDefinition instances from the given parameters.
 */
final class CriterionResolver
{
    /**
     * Set of available operator names.
     *
     * @var array
     */
    private static $operatorMap = [
        'eq' => Operator::EQ,
        'gt' => Operator::GT,
        'gte' => Operator::GTE,
        'lt' => Operator::LT,
        'lte' => Operator::LTE,
        'in' => Operator::IN,
        'between' => Operator::BETWEEN,
        'like' => Operator::LIKE,
        'contains' => Operator::CONTAINS,
    ];

    /**
     * Resolve Criterion $parameters.
     *
     * @param string $name
     * @param mixed $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[]
     */
    public function resolve($name, $parameters)
    {
        return $this->resolveForTarget($name, null, $parameters);
    }

    /**
     * Resolve Field Criterion $parameters.
     *
     * @param string $name
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[]
     */
    public function resolveTargets($name, array $parameters)
    {
        $argumentsGrouped = [[]];

        foreach ($parameters as $target => $params) {
            $argumentsGrouped[] = $this->resolveForTarget($name, $target, $params);
        }

        return array_merge(...$argumentsGrouped);
    }

    /**
     * Return CriterionDefinition instances for the given Field $target and its $parameters.
     *
     * @throws \InvalidArgumentException
     *
     * @param string $name
     * @param string|null $target
     * @param mixed $parameters
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[]
     */
    private function resolveForTarget($name, $target, $parameters)
    {
        if ($this->isOperatorMap($parameters)) {
            return $this->resolveOperatorMap($name, $target, $parameters);
        }

        return [
            $this->buildArgument($name, $target, null, $parameters),
        ];
    }

    /**
     * Return CriterionDefinition instances for the given Field $target and its operator $map.
     *
     * @param string $name
     * @param string|null $target
     * @param array $map
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition[]
     */
    private function resolveOperatorMap($name, $target, array $map)
    {
        $arguments = [];

        foreach ($map as $operator => $value) {
            if ('not' === $operator) {
                $arguments[] = $this->buildArgument(
                    'not',
                    null,
                    null,
                    $this->resolveForTarget($name, $target, $value)
                );
            } else {
                $arguments[] = $this->buildArgument($name, $target, $operator, $value);
            }
        }

        return $arguments;
    }

    /**
     * Return CriterionDefinition instance from the given arguments.
     *
     * @param string $name
     * @param string|null $target
     * @param string|null $operator
     * @param mixed $value
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition
     */
    private function buildArgument($name, $target, $operator, $value)
    {
        return new CriterionDefinition([
            'name' => $name,
            'target' => $target,
            'operator' => $this->resolveOperator($operator, $value),
            'value' => $value,
        ]);
    }

    /**
     * Decide if the given $parameters is an operator-value map.
     *
     * @param mixed $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    private function isOperatorMap($parameters)
    {
        if (!is_array($parameters)) {
            return false;
        }

        $isOperatorMap = false;
        $isValueCollection = false;

        foreach (array_keys($parameters) as $key) {
            if (array_key_exists($key, self::$operatorMap) || 'not' === $key) {
                $isOperatorMap = true;
            } else {
                $isValueCollection = true;
            }
        }

        if ($isOperatorMap && $isValueCollection) {
            throw new InvalidArgumentException(
                'Array of parameters is not an operator map nor a value collection'
            );
        }

        return $isOperatorMap;
    }

    /**
     * Resolve actual operator value from the given arguments.
     *
     * @param string|null $symbol
     * @param mixed $value
     *
     * @return string
     */
    private function resolveOperator($symbol, $value)
    {
        if (null === $symbol) {
            return $this->getOperatorByValueType($value);
        }

        return self::$operatorMap[$symbol];
    }

    /**
     * Return operator value by the given $value.
     *
     * @param mixed $value
     *
     * @throws \RuntimeException
     *
     * @return mixed
     */
    private function getOperatorByValueType($value)
    {
        if (is_array($value)) {
            return Operator::IN;
        }

        return Operator::EQ;
    }
}
