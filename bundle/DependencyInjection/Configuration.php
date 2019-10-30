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
        $treeBuilder = new TreeBuilder($this->rootNodeName);

        // Keep compatibility with symfony/config < 4.2
        if (!\method_exists($treeBuilder, 'getRootNode')) {
            /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->root($this->rootNodeName);
        } else {
            /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
            $rootNode = $treeBuilder->getRootNode();
        }

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
        $systemNode
            ->booleanNode('render_missing_field_info')
                ->info('Whether to render useful debug information in place of a missing field')
            ->end();

        $keyValidator = static function ($v): bool {
            foreach (\array_keys($v) as $key) {
                if (!\is_string($key) || !\preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/A', $key)) {
                    return true;
                }
            }

            return false;
        };

        $idMapper = static function ($v) {
            if (\is_int($v)) {
                return ['id' => $v];
            }

            return ['remote_id' => $v];
        };

        /* @noinspection NullPointerExceptionInspection */
        $systemNode
            ->arrayNode('named_objects')
                ->info('Named objects')
                ->children()
                    ->arrayNode('content')
                        ->info('Content items by name')
                        ->useAttributeAsKey('name')
                        ->normalizeKeys(false)
                        ->arrayPrototype()
                            ->info('Content ID or remote ID')
                            ->beforeNormalization()
                                ->ifTrue(static function ($v) {return !\is_array($v);})
                                ->then($idMapper)
                            ->end()
                            ->children()
                                ->integerNode('id')
                                    ->info('Content ID')
                                ->end()
                                ->scalarNode('remote_id')
                                    ->info('Content remote ID')
                                    ->validate()
                                        ->ifTrue(static function ($v) {return !\is_string($v);})
                                        ->thenInvalid('Content remote ID value must be of string type.')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue($keyValidator)
                            ->thenInvalid('Content name must be a string conforming to a valid Twig variable name.')
                        ->end()
                    ->end()
                    ->arrayNode('location')
                        ->info('Locations items by name')
                        ->useAttributeAsKey('name')
                        ->normalizeKeys(false)
                        ->arrayPrototype()
                            ->info('Location ID or remote ID')
                            ->beforeNormalization()
                                ->ifTrue(static function ($v) {return !\is_array($v);})
                                ->then($idMapper)
                            ->end()
                            ->children()
                                ->integerNode('id')
                                    ->info('Location ID')
                                ->end()
                                ->scalarNode('remote_id')
                                    ->info('Location remote ID')
                                    ->validate()
                                        ->ifTrue(static function ($v) {return !\is_string($v);})
                                        ->thenInvalid('Location remote ID value must be of string type.')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue($keyValidator)
                            ->thenInvalid('Location name must be a string conforming to a valid Twig variable name.')
                        ->end()
                    ->end()
                    ->arrayNode('tag')
                        ->info('Tags by name')
                        ->useAttributeAsKey('name')
                        ->normalizeKeys(false)
                        ->arrayPrototype()
                            ->info('Tag ID or remote ID')
                            ->beforeNormalization()
                                ->ifTrue(static function ($v) {return !\is_array($v);})
                                ->then($idMapper)
                            ->end()
                            ->children()
                                ->integerNode('id')
                                    ->info('Tag ID')
                                ->end()
                                ->scalarNode('remote_id')
                                    ->info('Tag remote ID')
                                    ->validate()
                                        ->ifTrue(static function ($v) {return !\is_string($v);})
                                        ->thenInvalid('Tag remote ID value must be of string type.')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue($keyValidator)
                            ->thenInvalid('Tag name must be a string conforming to a valid Twig variable name.')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
