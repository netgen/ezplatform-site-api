<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\View;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Named queries configuration.
 */
class Query extends View
{
    const NODE_KEY = 'ng_queries';

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder->arrayNode(static::NODE_KEY)
            ->info("Netgen's Site API named queries configuration")
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->children()
                    ->scalarNode('query_type')
                        ->info('Name of the QueryType implementation')
                        ->isRequired()
                    ->end()
                    ->booleanNode('use_filter')
                        ->info('Whether to use FilterService of FindService')
                        ->defaultValue(true)
                    ->end()
                    ->scalarNode('max_per_page')
                        ->info('Number of results per page when using pager')
                        ->defaultValue(25)
                    ->end()
                    ->scalarNode('page')
                        ->info('Current page when using pager')
                        ->defaultValue(1)
                    ->end()
                    ->arrayNode('parameters')
                        ->info('Parameters for the QueryType implementation')
                        ->defaultValue([])
                        ->useAttributeAsKey('key')
                        ->prototype('variable');
    }
}
