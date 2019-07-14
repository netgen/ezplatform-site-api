<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ViewBuilderRegistrationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('ezpublish.view_builder.registry')) {
            return;
        }

        $viewBuilderRegistry = $container->findDefinition('ezpublish.view_builder.registry');
        $contentViewBuilder = $container->findDefinition('netgen.ezplatform_site.view_builder.content');

        $viewBuilderRegistry->addMethodCall(
            'addToRegistry',
            [[$contentViewBuilder]]
        );
    }
}
