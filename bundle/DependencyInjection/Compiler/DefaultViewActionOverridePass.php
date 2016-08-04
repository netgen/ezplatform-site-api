<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

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
            ->setClass(
                $container->getParameter('netgen_ez_platform_site_api.urlalias_router.class')
            );
    }
}
