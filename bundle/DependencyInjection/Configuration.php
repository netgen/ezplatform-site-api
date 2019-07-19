<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    /**
     * @var string
     */
    protected $rootNodeName;

    public function __construct(string $rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root($this->rootNodeName);

        $this->addConfiguration($rootNode);

        return $treeBuilder;
    }

    protected function addConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $systemNode = $this->generateScopeBaseNode($rootNode);
        $systemNode
            ->booleanNode('override_url_alias_view_action')
                ->info('Controls override of the URL alias view action')
            ->end();
        $systemNode
            ->booleanNode('use_always_available_fallback')
                ->info('Controls fallback to main language marked as always available')
            ->end();
        $systemNode
            ->booleanNode('fail_on_missing_fields')
                ->info('Whether to fail on missing Content Fields')
            ->end();
    }
}
