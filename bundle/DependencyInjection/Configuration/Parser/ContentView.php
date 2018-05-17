<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\View;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Twig_Lexer;

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
                            ->append($this->getQueryNode(static::QUERY_KEY))
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

    /**
     * @param string $name
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getQueryNode($name)
    {
        $queries = new ArrayNodeDefinition($name);
        $queries
            ->info('Query configuration')
            ->useAttributeAsKey('key')
            ->prototype('array')
                ->beforeNormalization()
                    // String value is a shortcut to the named query
                    ->ifString()
                    ->then(function ($v) {return ['named_query' => $v];})
                ->end()
                ->children()
                    ->scalarNode('query_type')
                        ->info('Name of the QueryType implementation')
                    ->end()
                    ->scalarNode('use_filter')
                        ->info('Whether to use FilterService of FindService')
                    ->end()
                    ->scalarNode('max_per_page')
                        ->info('Number of results per page when using pager')
                    ->end()
                    ->scalarNode('page')
                        ->info('Current page when using pager')
                    ->end()
                    ->arrayNode('parameters')
                        ->info('Parameters for the QueryType implementation')
                        ->useAttributeAsKey('key')
                        ->prototype('variable')->end()
                    ->end()
                    ->scalarNode('named_query')
                        ->info('Name of the configured query')
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(function ($v) {
                        return array_key_exists('named_query', $v) && array_key_exists('query_type', $v);
                    })
                    ->thenInvalid(
                        'You cannot use both "named_query" and "query_type" at the same time.'
                    )
                ->end()
                ->validate()
                    ->ifTrue(function ($v) {
                        return !array_key_exists('named_query', $v) && !array_key_exists('query_type', $v);
                    })
                    ->thenInvalid(
                        'One of "named_query" or "query_type" must be set.'
                    )
                ->end()
                ->validate()
                    ->ifTrue(function ($v) {return array_key_exists('query_type', $v);})
                    ->then(function ($v) {
                        if (!array_key_exists('use_filter', $v)) {
                            $v['use_filter'] = true;
                        }

                        if (!array_key_exists('max_per_page', $v)) {
                            $v['max_per_page'] = 25;
                        }

                        if (!array_key_exists('page', $v)) {
                            $v['page'] = 1;
                        }

                        if (!array_key_exists('parameters', $v)) {
                            $v['parameters'] = [];
                        }

                        return $v;
                    })
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

        return $queries;
    }
}
