<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use OutOfBoundsException;

/**
 * QueryCollection contains a map of QueryDefinitions by their name string.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition
 */
final class QueryCollection
{
    /**
     * Internal map of QueryDefinitions.
     *
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition[]
     */
    private $queryDefinitionMap = [];

    /**
     * Add $queryDefinition by $name to the internal map.
     *
     * @param string $name
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @return void
     */
    public function addQueryDefinition($name, QueryDefinition $queryDefinition)
    {
        $this->queryDefinitionMap[$name] = $queryDefinition;
    }

    /**
     * Return QueryDefinition by given $name.
     *
     * @throws \OutOfBoundsException If no QueryDefinition with given $name is found.
     *
     * @param $name
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition
     */
    public function getQueryDefinition($name)
    {
        if (array_key_exists($name, $this->queryDefinitionMap)) {
            return $this->queryDefinitionMap[$name];
        }

        throw new OutOfBoundsException(
            "Could not find QueryDefinition with name '{$name}'"
        );
    }
}
