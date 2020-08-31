<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class BaseOptions extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->booleanNode('ng_set_site_api_as_primary_content_view')
            ->info('Controls whether Site API content view should be used as the primary content view')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_fallback_to_secondary_content_view')
            ->info('Controls fallback content view rendering between primary and secondary content view (Site API or eZ Platform)')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_fallback_without_subrequest')
                ->info('Controls whether secondary content view fallback should use a subrequest')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_richtext_embed_without_subrequest')
            ->info('Controls whether RichText and XmlText embed rendering should use a subrequest')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_xmltext_embed_without_subrequest')
            ->info('Controls whether RichText and XmlText embed rendering should use a subrequest')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_use_always_available_fallback')
            ->info('Controls missing translation fallback to main language marked as always available')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_fail_on_missing_field')
            ->info('Controls failing on a missing Content Field')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_render_missing_field_info')
            ->info('Controls rendering useful debug information in place of a missing field')
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $this->contextualize('ng_set_site_api_as_primary_content_view', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_fallback_to_secondary_content_view', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_fallback_without_subrequest', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_richtext_embed_without_subrequest', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_xmltext_embed_without_subrequest', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_use_always_available_fallback', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_fail_on_missing_field', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_render_missing_field_info', $scopeSettings, $currentScope, $contextualizer);
    }

    private function contextualize(
        string $parameterName,
        array $scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (\array_key_exists($parameterName, $scopeSettings)) {
            $contextualizer->setContextualParameter($parameterName, $currentScope, $scopeSettings[$parameterName]);
        }
    }
}
