<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\View;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ContentView extends View
{
    const QUERY_KEY = 'queries';
    const NODE_KEY = 'ngcontent_view';
    const INFO = 'Template selection settings when displaying a content with Netgen Site API';

    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode(static::NODE_KEY)
                ->info(static::INFO)
                ->useAttributeAsKey('key')
                ->normalizeKeys(false)
                ->prototype('array')
                    ->useAttributeAsKey('key')
                    ->normalizeKeys(false)
                    ->info("View selection rulesets, grouped by view type. Key is the view type (e.g. 'full', 'line', ...)")
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->info('Your template path, as MyBundle:subdir:my_template.html.twig')->end()
                            ->scalarNode('controller')
                                ->info(
<<<EOT
Use custom controller instead of the default one to display a content matching your rules.
You can use the controller reference notation supported by Symfony.
EOT
                                )
                                ->example('MyBundle:MyControllerClass:view')
                            ->end()
                            ->arrayNode('match')
                                ->info('Condition matchers configuration')
                                ->isRequired()
                                ->useAttributeAsKey('key')
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode(static::QUERY_KEY)
                                ->info('Query configuration')
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
                                            ->prototype('variable')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('params')
                                ->info(
<<<EOT
Arbitrary params that will be passed in the ContentView object, manageable by ViewProviders.
Those params will NOT be passed to the resulting view template by default.
EOT
                                )
                                ->example(
                                    [
                                        'foo' => '%some.parameter.reference%',
                                        'osTypes' => ['osx', 'linux', 'windows'],
                                    ]
                                )
                                ->useAttributeAsKey('key')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
