<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    protected $rootNodeName;

    public function __construct($rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->rootNodeName);

        $this->addConfiguration($rootNode);

        return $treeBuilder;
    }

    protected function addConfiguration($rootNode): void
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
