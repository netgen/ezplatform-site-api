<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Creates service aliases necessary for Aggregate Repository implementation to work.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Repository\Aggregate\Repository
 */
class AggregateRepositoryPass implements CompilerPassInterface
{
    /**
     * Public service alias ID of the topmost eZ Platform repository.
     *
     * @var string
     */
    private static $topEzRepositoryAliasId = 'ezpublish.api.repository';

    /**
     * Out internal service alias ID of the topmost eZ Platform repository (created here).
     *
     * @var string
     */
    private static $renamedTopEzRepositoryAliasId = 'netgen.ezpublish.api.repository';

    /**
     * Service ID of the Aggregate Repository implementation.
     *
     * @var string
     */
    private static $aggregateRepositoryId = 'netgen.ezplatform_site.repository.aggregate';

    /**
     * Service tag used for custom repositories.
     *
     * @var string
     */
    private static $customRepositoryTag = 'netgen.ezplatform_site.repository';

    /**
     * @inheritdoc
     *
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        // 1. Register custom repositories with Aggregate repository
        $aggregateRepositoryDefinition = $container->findDefinition(static::$aggregateRepositoryId);
        $customRepositories = $container->findTaggedServiceIds(static::$customRepositoryTag);

        foreach ($customRepositories as $id => $attributes) {
            $aggregateRepositoryDefinition->addMethodCall(
                'addRepository',
                [new Reference($id)]
            );
        }

        $topEzRepositoryAlias = $container->getAlias(static::$topEzRepositoryAliasId);

        // 2. Re-link eZ Platform's public top Repository alias
        $container->setAlias(static::$renamedTopEzRepositoryAliasId, (string)$topEzRepositoryAlias);

        // 3. Overwrite eZ Platform's public top Repository alias
        // to aggregate Repository implementation
        $container->setAlias(static::$topEzRepositoryAliasId, static::$aggregateRepositoryId);
    }
}
