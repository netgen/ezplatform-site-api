<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\View;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Twig_Lexer;

/**
 * Named queries configuration.
 */
class NamedQuery extends View
{
    const NODE_KEY = 'ng_named_query';

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode(static::NODE_KEY)
                ->info("Netgen's Site API named query configuration")
                ->useAttributeAsKey('key')
                ->prototype('array')
                    ->children()
                        ->scalarNode('query_type')
                            ->info('Name of the QueryType implementation')
                            ->isRequired()
                        ->end()
                        ->scalarNode('use_filter')
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
                            ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(function ($v) {
                    foreach (array_keys($v) as $key) {
                        if (!is_string($key) || !preg_match(Twig_Lexer::REGEX_NAME, $key)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid(
                    'Query keys must be strings conforming to a valid Twig variable names.'
                );
    }
}
