<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\PreviewController;
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

        $container
            ->findDefinition('ezpublish.controller.content.preview.core')
            ->setClass(PreviewController::class)
            ->addMethodCall(
                'setConfigResolver',
                [new Reference('ezpublish.config.resolver')]
            )
            ->addMethodCall(
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
