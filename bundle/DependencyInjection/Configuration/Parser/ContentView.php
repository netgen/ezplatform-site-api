<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Twig\Lexer;

class ContentView extends AbstractParser
{
    public const QUERY_KEY = 'queries';
    public const NODE_KEY = 'ngcontent_view';
    private const INFO = 'Template selection settings when displaying a content with Netgen Site API';
    private const DEFAULT_MATCH_VALUE = ['NG_DEFAULT_MATCH_VALUE'];
    private const DEFAULT_QUERIES_VALUE = ['NG_DEFAULT_QUERIES_VALUE'];
    private const DEFAULT_PARAMS_VALUE = ['NG_DEFAULT_PARAMS_VALUE'];

    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode(static::NODE_KEY)
                ->info(static::INFO)
                ->useAttributeAsKey('key')
                ->normalizeKeys(false)
                ->arrayPrototype()
                    ->useAttributeAsKey('key')
                    ->normalizeKeys(false)
                    ->info("View selection rulesets, grouped by view type. Key is the view type (e.g. 'full', 'line', ...)")
                    ->arrayPrototype()
                        ->beforeNormalization()
                            ->ifTrue(static function ($v) {
                                return (\array_key_exists('permanent_redirect', $v) xor \array_key_exists('temporary_redirect', $v))
                                    && !\array_key_exists('redirect', $v);
                            })
                            ->then(static function ($v) {
                                $value = $v['permanent_redirect'] ?? $v['temporary_redirect'];
                                $permanent = \array_key_exists('permanent_redirect', $v);

                                $v['redirect'] = [
                                    'target' => $value,
                                    'permanent' => $permanent,
                                    'absolute' => false,
                                ];

                                unset($v['permanent_redirect'], $v['temporary_redirect']);

                                return $v;
                            })
                        ->end()
                        ->beforeNormalization()
                            ->ifTrue(static function ($v): bool {
                                return !\array_key_exists('match', $v) && !\array_key_exists('extends', $v);
                            })
                            ->thenInvalid(
                                'When view configuration is not extending another, match key is required'
                            )
                        ->end()
                        ->children()
                            ->scalarNode('extends')->info('Extended view type/name, for example full/article')->end()
                            ->scalarNode('template')->info('Your template path, as @App/my_template.html.twig')->end()
                            ->scalarNode('controller')
                                ->info(
                                    <<<'EOT'
Use custom controller instead of the default one to display a content matching your rules.
You can use the controller reference notation supported by Symfony.
EOT
                                )
                                ->example('MyBundle:MyControllerClass:view')
                            ->end()
                            ->arrayNode('redirect')
                                ->children()
                                    ->scalarNode('target')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->booleanNode('permanent')
                                        ->defaultFalse()
                                    ->end()
                                    ->booleanNode('absolute')
                                        ->defaultFalse()
                                    ->end()
                                    ->arrayNode('target_parameters')
                                        ->useAttributeAsKey('key')
                                        ->variablePrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('permanent_redirect')
                                ->info(
                                    <<<'EOT'
Set up permanent redirect. You can use the expression language here as well.
EOT
                                )
                                ->example('@=location.parent')
                            ->end()
                            ->scalarNode('temporary_redirect')
                                ->info(
                                    <<<'EOT'
Set up temporary redirect. You can use the expression language here as well.
EOT
                                )
                                ->example('@=location.parent')
                            ->end()
                            ->arrayNode('match')
                                ->info('Condition matchers configuration')
                                ->defaultValue(self::DEFAULT_MATCH_VALUE)
                                ->useAttributeAsKey('key')
                                ->variablePrototype()->end()
                            ->end()
                            ->append($this->getQueryNode(static::QUERY_KEY))
                            ->arrayNode('params')
                                ->info(
                                    <<<'EOT'
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
                                ->defaultValue(self::DEFAULT_PARAMS_VALUE)
                                ->useAttributeAsKey('key')
                                ->variablePrototype()->end()
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v): bool {
                                if (\array_key_exists('redirect', $v)) {
                                    return \array_key_exists('controller', $v) || \array_key_exists('template', $v);
                                }

                                return false;
                            })
                            ->thenInvalid(
                                'You cannot use both redirect and controller/template configuration at the same time.'
                            )
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v): bool {
                                if (\array_key_exists('redirect', $v)) {
                                    return \array_key_exists('temporary_redirect', $v) || \array_key_exists('permanent_redirect', $v);
                                }

                                return false;
                            })
                            ->thenInvalid(
                                'You cannot use both expanded and shortcut redirect configuration at the same time.'
                            )
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v): bool {
                                return \array_key_exists('temporary_redirect', $v) && \array_key_exists('permanent_redirect', $v);
                            })
                            ->thenInvalid(
                                'You cannot use both "temporary_redirect" and "permanent_redirect" at the same time.'
                            )
                        ->end()
                        ->validate()
                            ->ifTrue(static function ($v): bool {
                                if (\array_key_exists('temporary_redirect', $v) || \array_key_exists('permanent_redirect', $v)) {
                                    return \array_key_exists('controller', $v) || \array_key_exists('template', $v);
                                }

                                return false;
                            })
                            ->thenInvalid(
                                'You cannot use both redirect and controller/template configuration at the same time.'
                            )
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        foreach ($scopeSettings['ngcontent_view'] as $viewType => &$viewConfigs) {
            foreach ($viewConfigs as $name => &$viewConfig) {
                $this->extendViewConfig(
                    $viewConfig,
                    $viewType . '/' . $name,
                    $scopeSettings['ngcontent_view']
                );
            }
        }
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->mapConfigArray(
            static::NODE_KEY,
            $config,
            ContextualizerInterface::MERGE_FROM_SECOND_LEVEL
        );
    }

    private function extendViewConfig(&$config, string $viewPath, array $viewConfigs): void
    {
        if (!\array_key_exists('extends', $config)) {
            $this->restoreDefaultValues($config);

            return;
        }

        $this->unsetDefaultValues($config);

        [$extendedViewType, $extendedName] = \explode('/', $config['extends'] . '/');

        if (!isset($viewConfigs[$extendedViewType][$extendedName])) {
            throw new InvalidConfigurationException(
                \sprintf(
                    'In %s: extended view configuration "%s" was not found',
                    $viewPath,
                    $config['extends']
                )
            );
        }

        $baseConfig = $viewConfigs[$extendedViewType][$extendedName];

        if (\array_key_exists('extends', $baseConfig)) {
            throw new InvalidConfigurationException(
                \sprintf(
                    'In %s: only one level of view configuration inheritance is allowed, %s already extends %s',
                    $viewPath,
                    $extendedViewType . '/' . $extendedName,
                    $baseConfig['extends']
                )
            );
        }

        $replacedConfig = \array_replace($baseConfig, $config);

        if ($replacedConfig === null) {
            throw new InvalidConfigurationException('Could not replace extended config');
        }

        $config = $replacedConfig;
    }

    private function restoreDefaultValues(array &$config): void
    {
        if ($config['match'] === self::DEFAULT_MATCH_VALUE) {
            $config['match'] = [];
        }

        if ($config['queries'] === self::DEFAULT_QUERIES_VALUE) {
            $config['queries'] = [];
        }

        if ($config['params'] === self::DEFAULT_PARAMS_VALUE) {
            $config['params'] = [];
        }
    }

    private function unsetDefaultValues(array &$config): void
    {
        if ($config['match'] === self::DEFAULT_MATCH_VALUE) {
            unset($config['match']);
        }

        if ($config['queries'] === self::DEFAULT_QUERIES_VALUE) {
            unset($config['queries']);
        }

        if ($config['params'] === self::DEFAULT_PARAMS_VALUE) {
            unset($config['params']);
        }
    }

    /**
     * @param string $name
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function getQueryNode(string $name): ArrayNodeDefinition
    {
        $queries = new ArrayNodeDefinition($name);
        $queries
            ->info('Query configuration')
            ->defaultValue(self::DEFAULT_QUERIES_VALUE)
            ->useAttributeAsKey('key')
            ->arrayPrototype()
                ->beforeNormalization()
                    // String value is a shortcut to the named query
                    ->ifString()
                    ->then(static function ($v): array {return ['named_query' => $v];})
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
                        ->variablePrototype()->end()
                    ->end()
                    ->scalarNode('named_query')
                        ->info('Name of the configured query')
                    ->end()
                ->end()
                ->validate()
                    ->ifTrue(static function ($v): bool {
                        return \array_key_exists('named_query', $v) && \array_key_exists('query_type', $v);
                    })
                    ->thenInvalid(
                        'You cannot use both "named_query" and "query_type" at the same time.'
                    )
                ->end()
                ->validate()
                    ->ifTrue(static function ($v): bool {
                        return !\array_key_exists('named_query', $v) && !\array_key_exists('query_type', $v);
                    })
                    ->thenInvalid(
                        'One of "named_query" or "query_type" must be set.'
                    )
                ->end()
                ->validate()
                    ->ifTrue(static function ($v): bool {return \array_key_exists('query_type', $v);})
                    ->then(static function ($v): array {
                        if (!\array_key_exists('use_filter', $v)) {
                            $v['use_filter'] = true;
                        }

                        if (!\array_key_exists('max_per_page', $v)) {
                            $v['max_per_page'] = 25;
                        }

                        if (!\array_key_exists('page', $v)) {
                            $v['page'] = 1;
                        }

                        if (!\array_key_exists('parameters', $v)) {
                            $v['parameters'] = [];
                        }

                        return $v;
                    })
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

        return $queries;
    }
}
