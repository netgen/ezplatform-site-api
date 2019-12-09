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
            ->booleanNode('ng_fallback_to_secondary_content_view')
                ->info('Controls fallback content view rendering between Site API and eZ Platform')
            ->end();

        $nodeBuilder
            ->booleanNode('ng_fallback_with_subrequest')
                ->info('Controls whether secondary content view fallback should use a subrequest')
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $this->contextualize('ng_fallback_to_secondary_content_view', $scopeSettings, $currentScope, $contextualizer);
        $this->contextualize('ng_fallback_with_subrequest', $scopeSettings, $currentScope, $contextualizer);
    }

    private function contextualize(
        string $parameterName,
        array &$scopeSettings,
        $currentScope,
        ContextualizerInterface $contextualizer
    ): void {
        if (\array_key_exists($parameterName, $scopeSettings)) {
            $contextualizer->setContextualParameter($parameterName, $currentScope, $scopeSettings[$parameterName]);
        }
    }
}
