<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
    private static $aggregateRepositoryId = 'netgen.ezplatform_site.aggregate_repository';

    /**
     * @inheritdoc
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $topEzRepositoryAlias = $container->getAlias(static::$topEzRepositoryAliasId);

        // 1. Re-link eZ Platform's public top Repository alias
        $container->setAlias(static::$renamedTopEzRepositoryAliasId, (string)$topEzRepositoryAlias);

        // 2. Overwrite eZ Platform's public top Repository alias
        // to aggregate Repository implementation
        $container->setAlias(static::$topEzRepositoryAliasId, static::$aggregateRepositoryId);
    }
}
