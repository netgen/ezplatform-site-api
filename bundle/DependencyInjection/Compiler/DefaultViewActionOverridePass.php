<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Netgen\Bundle\EzPlatformSiteApiBundle\Routing\UrlAliasRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DefaultViewActionOverridePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.urlalias_router')) {
            return;
        }

        $container
            ->findDefinition('ezpublish.urlalias_router')
            ->setClass(UrlAliasRouter::class);
    }
}
