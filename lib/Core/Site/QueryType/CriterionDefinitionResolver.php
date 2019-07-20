<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use InvalidArgumentException;

/**
 * @internal Do not depend on this service, it can be changed without warning.
 *
 * CriterionDefinitionResolver resolves CriterionDefinition instances from the given parameters.
 */
final class CriterionDefinitionResolver
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
    public function resolve(string $name, $parameters): array
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
    public function resolveTargets(string $name, array $parameters): array
    {
        $definitionsGrouped = [[]];

        foreach ($parameters as $target => $params) {
            $definitionsGrouped[] = $this->resolveForTarget($name, $target, $params);
        }

        return array_merge(...$definitionsGrouped);
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
    private function resolveForTarget(string $name, ?string $target, $parameters): array
    {
        if ($this->isOperatorMap($parameters)) {
            return $this->resolveOperatorMap($name, $target, $parameters);
        }

        return [
            $this->buildDefinition($name, $target, null, $parameters),
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
    private function resolveOperatorMap(string $name, ?string $target, array $map): array
    {
        $definitions = [];

        foreach ($map as $operator => $value) {
            if ($operator === 'not') {
                $definitions[] = $this->buildDefinition(
                    'not',
                    null,
                    null,
                    $this->resolveForTarget($name, $target, $value)
                );
            } else {
                $definitions[] = $this->buildDefinition($name, $target, $operator, $value);
            }
        }

        return $definitions;
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
    private function buildDefinition(string $name, ?string $target, ?string $operator, $value): CriterionDefinition
    {
        return new CriterionDefinition([
            'name' => $name,
            'target' => $target,
            'operator' => $this->resolveOperator($operator, $value),
            'value' => $value,
        ]);
    }

    /**
     * Decide if the given $parameters is an operator-value map (otherwise it's a value collection).
     *
     * @param mixed $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    private function isOperatorMap($parameters): bool
    {
        if (!is_array($parameters)) {
            return false;
        }

        $isOperatorMap = false;
        $isValueCollection = false;

        foreach (array_keys($parameters) as $key) {
            if (array_key_exists($key, self::$operatorMap) || $key === 'not') {
                $isOperatorMap = true;
            } else {
                $isValueCollection = true;
            }
        }

        if ($isOperatorMap && $isValueCollection) {
            throw new InvalidArgumentException(
                'Array of parameters is ambiguous: it should be either an operator map or a value collection'
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
     * @return mixed
     */
    private function resolveOperator(?string $symbol, $value)
    {
        if ($symbol === null) {
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
     * @return string
     */
    private function getOperatorByValueType($value): string
    {
        if (is_array($value)) {
            return Operator::IN;
        }

        return Operator::EQ;
    }
}
