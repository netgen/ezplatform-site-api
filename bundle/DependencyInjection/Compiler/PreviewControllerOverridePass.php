<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PreviewControllerOverridePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.controller.content.preview.core')) {
            return;
        }

        $previewController = $container->findDefinition(
            'ezpublish.controller.content.preview.core'
        );

        $previewController->setClass(
            $container->getParameter('netgen_ez_platform_site_api.preview_controller.class')
        );

        $previewController->addMethodCall(
            'setConfigResolver',
            [new Reference('ezpublish.config.resolver')]
        );

        $previewController->addMethodCall(
            'setLoadService',
            [new Reference('netgen.ezplatform_site.load_service')]
        );

        // Resetting the alias to the original value
        // to disable legacy bridge taking over the preview controller
        $container->setAlias(
            'ezpublish.controller.content.preview',
            'ezpublish.controller.content.preview.core'
        );
    }
}
