<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Netgen\Bundle\EzPlatformSiteApiBundle\Core\FieldType\XmlText\RenderEmbedConverter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class XmlTextFieldTypePass implements CompilerPassInterface
{
    /**
     * Overrides EmbedToHtml5 ezxmltext converter with own implementation.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('ezpublish.fieldType.ezxmltext.converter.embedToHtml5')) {
            return;
        }

        $container
            ->findDefinition('ezpublish.fieldType.ezxmltext.converter.embedToHtml5')
            ->setClass(RenderEmbedConverter::class)
            ->addMethodCall('setSite', [new Reference('netgen.ezplatform_site.site')])
            ->addMethodCall('setConfigResolver', [new Reference('ezpublish.config.resolver')])
            ->addMethodCall('setViewBuilder', [new Reference('netgen.ezplatform_site.view_builder.content')])
            ->addMethodCall('setViewRenderer', [new Reference('netgen.ezplatform_site.view_renderer')]);
    }
}
