<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\SiteApi;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Twig\Lexer;
use function array_keys;
use function is_string;
use function preg_match;

class NamedQueryBuilder
{
    public static function build(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('named_queries')
                ->info("Netgen's Site API named queries configuration")
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
                    foreach (array_keys($v) as $key) {
                        if (!is_string($key) || !preg_match(Lexer::REGEX_NAME, $key)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid('Query key must be a string conforming to a valid Twig variable name.');
    }
}
