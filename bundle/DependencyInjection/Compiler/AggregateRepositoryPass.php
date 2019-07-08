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
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        // 1. Register custom repositories with Aggregate repository
        $aggregateRepositoryDefinition = $container->findDefinition(static::$aggregateRepositoryId);
        $customRepositoryTags = $container->findTaggedServiceIds(static::$customRepositoryTag);
        $customRepositoryReferences = [];

        foreach (array_keys($customRepositoryTags) as $id) {
            $customRepositoryReferences[] = new Reference($id);
        }

        $aggregateRepositoryDefinition->replaceArgument(1, $customRepositoryReferences);

        $topEzRepositoryAlias = $container->getAlias(static::$topEzRepositoryAliasId);

        // 2. Re-link eZ Platform's public top Repository alias
        $container->setAlias(static::$renamedTopEzRepositoryAliasId, (string)$topEzRepositoryAlias);

        // 3. Overwrite eZ Platform's public top Repository alias
        // to aggregate Repository implementation
        $container->setAlias(static::$topEzRepositoryAliasId, static::$aggregateRepositoryId);
        $container->getAlias(static::$topEzRepositoryAliasId)->setPublic(true);
    }
}
