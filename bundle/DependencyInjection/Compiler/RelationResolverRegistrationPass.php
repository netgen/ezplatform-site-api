<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register field type relation resolver plugins.
 */
final class RelationResolverRegistrationPass implements CompilerPassInterface
{
    /**
     * Service ID of the resolver registry.
     *
     * @see \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry
     *
     * @var string
     */
    private $resolverRegistryId = 'netgen.ezplatform_site.plugins.field_type.relation_resolver.registry';

    /**
     * Service tag used for field type relation resolvers.
     *
     * @see \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver
     *
     * @var string
     */
    private $resolverTag = 'netgen.ezplatform_site.plugins.field_type.relation_resolver';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->resolverRegistryId)) {
            return;
        }

        $resolverRegistryDefinition = $container->getDefinition($this->resolverRegistryId);

        $resolvers = $container->findTaggedServiceIds($this->resolverTag);

        foreach ($resolvers as $id => $attributes) {
            /** @var array $attributes */
            $this->registerResolver($resolverRegistryDefinition, $id, $attributes);
        }
    }

    /**
     * Add method call to register resolver with given $id with resolver registry.
     *
     * @throws \LogicException
     *
     * @param \Symfony\Component\DependencyInjection\Definition $resolverRegistryDefinition
     * @param string $id
     * @param array $attributes
     *
     * @return void
     */
    private function registerResolver(Definition $resolverRegistryDefinition, $id, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!isset($attribute['identifier'])) {
                throw new LogicException(
                    "'{$this->resolverTag}' service tag needs an 'identifier' attribute to identify the field type"
                );
            }

            $resolverRegistryDefinition->addMethodCall(
                'register',
                [
                    $attribute['identifier'],
                    new Reference($id),
                ]
            );
        }
    }
}
