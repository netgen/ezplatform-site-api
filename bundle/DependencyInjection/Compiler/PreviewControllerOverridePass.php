<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Netgen\Bundle\EzPlatformSiteApiBundle\Controller\PreviewController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PreviewControllerOverridePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $corePreviewControllerServiceId = 'ezpublish.controller.content.preview.core';

        if (!$container->hasDefinition($corePreviewControllerServiceId)) {
            return;
        }

        $container
            ->findDefinition($corePreviewControllerServiceId)
            ->setClass(PreviewController::class)
            ->addMethodCall(
                'setConfigResolver',
                [new Reference('ezpublish.config.resolver')]
            )
            ->addMethodCall(
                'setSite',
                [new Reference('netgen.ezplatform_site.core.site')]
            );

        // Resetting the alias to the original value
        // to disable legacy bridge taking over the preview controller
        $container->setAlias(
            'ezpublish.controller.content.preview',
            $corePreviewControllerServiceId
        );
    }
}
