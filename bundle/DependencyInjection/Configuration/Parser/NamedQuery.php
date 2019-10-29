<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Twig\Lexer;

/**
 * Named queries configuration.
 */
class NamedQuery extends AbstractParser
{
    public const NODE_KEY = 'ng_named_query';

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode(static::NODE_KEY)
                ->info("Netgen's Site API named query configuration")
                ->useAttributeAsKey('key')
                ->arrayPrototype()
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
                            ->variablePrototype()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->validate()
                ->ifTrue(static function ($v): bool {
                    foreach (\array_keys($v) as $key) {
                        if (!\is_string($key) || !\preg_match(Lexer::REGEX_NAME, $key)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid(
                    'Query key must be a string conforming to a valid Twig variable name.'
                );
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->mapConfigArray(static::NODE_KEY, $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        // does nothing
    }
}
