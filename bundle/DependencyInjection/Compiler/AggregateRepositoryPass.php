<?php

declare(strict_types=1);

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
    private const TopEzRepositoryAliasId = 'ezpublish.api.repository';

    /**
     * Out internal service alias ID of the topmost eZ Platform repository (created here).
     *
     * @var string
     */
    private const RenamedTopEzRepositoryAliasId = 'netgen.ezpublish.api.repository';

    /**
     * Service ID of the Aggregate Repository implementation.
     *
     * @var string
     */
    private const AggregateRepositoryId = 'netgen.ezplatform_site.repository.aggregate';

    /**
     * Service tag used for custom repositories.
     *
     * @var string
     */
    private const CustomRepositoryTag = 'netgen.ezplatform_site.repository';

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        // 1. Register custom repositories with Aggregate repository
        $aggregateRepositoryDefinition = $container->findDefinition(self::AggregateRepositoryId);
        $customRepositoryTags = $container->findTaggedServiceIds(self::CustomRepositoryTag);
        $customRepositoryReferences = [];

        foreach (\array_keys($customRepositoryTags) as $id) {
            $customRepositoryReferences[] = new Reference($id);
        }

        $aggregateRepositoryDefinition->replaceArgument(1, $customRepositoryReferences);

        $topEzRepositoryAlias = $container->getAlias(self::TopEzRepositoryAliasId);

        // 2. Re-link eZ Platform's public top Repository alias
        $container->setAlias(self::RenamedTopEzRepositoryAliasId, (string) $topEzRepositoryAlias);

        // 3. Overwrite eZ Platform's public top Repository alias
        // to aggregate Repository implementation
        $container->setAlias(self::TopEzRepositoryAliasId, self::AggregateRepositoryId);
        $container->getAlias(self::TopEzRepositoryAliasId)->setPublic(true);
    }
}
