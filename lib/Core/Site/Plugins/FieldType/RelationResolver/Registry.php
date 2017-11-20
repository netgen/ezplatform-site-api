<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver;

use OutOfBoundsException;

/**
 * Registry for field type relation resolvers.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver
 */
class Registry
{
    /**
     * Map of resolvers by field type identifier.
     *
     * @var \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver[]
     */
    protected $resolverMap = array();

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver[] $resolverMap
     */
    public function __construct(array $resolverMap = [])
    {
        foreach ($resolverMap as $fieldTypeIdentifier => $resolver) {
            $this->register($fieldTypeIdentifier, $resolver);
        }
    }

    /**
     * Register a $resolver for $fieldTypeIdentifier.
     *
     * @param string $fieldTypeIdentifier
     * @param \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver $resolver
     *
     * @return void
     */
    public function register($fieldTypeIdentifier, Resolver $resolver)
    {
        $this->resolverMap[$fieldTypeIdentifier] = $resolver;
    }

    /**
     * Returns Resolver for $fieldTypeIdentifier.
     *
     * @throws \OutOfBoundsException When there is no resolver for the given $fieldTypeIdentifier
     *
     * @param string $fieldTypeIdentifier
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver
     */
    public function get($fieldTypeIdentifier)
    {
        if (isset($this->resolverMap[$fieldTypeIdentifier])) {
            return $this->resolverMap[$fieldTypeIdentifier];
        }

        throw new OutOfBoundsException(
            "No relation resolver is registered for field type identifier '{$fieldTypeIdentifier}'"
        );
    }
}
