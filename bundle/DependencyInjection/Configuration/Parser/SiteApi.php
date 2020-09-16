<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\AbstractParser;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\SiteApi\NamedObjectBuilder;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\SiteApi\NamedQueryBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class SiteApi extends AbstractParser
{
    private const NODE_KEY = 'ng_site_api';

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $childrenBuilder = $nodeBuilder->arrayNode('ng_site_api')->info('Site API configuration')->children();

        $childrenBuilder
            ->booleanNode('site_api_is_primary_content_view')
                ->info('Controls whether Site API content view should be used as the primary content view')
            ->end()
            ->booleanNode('fallback_to_secondary_content_view')
                ->info('Controls fallback content view rendering between primary and secondary content view (Site API or eZ Platform)')
            ->end()
            ->booleanNode('fallback_without_subrequest')
                ->info('Controls whether secondary content view fallback should use a subrequest')
            ->end()
            ->booleanNode('richtext_embed_without_subrequest')
                ->info('Controls whether RichText embed rendering should use a subrequest')
            ->end()
            ->booleanNode('use_always_available_fallback')
                ->info('Controls missing translation fallback to main language marked as always available')
            ->end()
            ->booleanNode('show_hidden_items')
                ->info('Controls whether hidden Locations and Content items will be shown by default')
            ->end()
            ->booleanNode('fail_on_missing_field')
                ->info('Controls failing on a missing Content Field')
            ->end()
            ->booleanNode('render_missing_field_info')
                ->info('Controls rendering useful debug information in place of a missing field')
            ->end()
        ->end();

        NamedObjectBuilder::build($childrenBuilder);
        NamedQueryBuilder::build($childrenBuilder);
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $booleanKeys = [
            'site_api_is_primary_content_view',
            'fallback_to_secondary_content_view',
            'fallback_without_subrequest',
            'richtext_embed_without_subrequest',
            'use_always_available_fallback',
            'fail_on_missing_field',
            'render_missing_field_info',
        ];

        foreach ($booleanKeys as $parameterName) {
            if (isset($scopeSettings[static::NODE_KEY][$parameterName])) {
                $contextualizer->setContextualParameter(
                    static::NODE_KEY . '.' . $parameterName,
                    $currentScope,
                    $scopeSettings[static::NODE_KEY][$parameterName]
                );
            }
        }

        if (isset($scopeSettings[static::NODE_KEY]['named_objects'])) {
            $scopeSettings[static::NODE_KEY . '.named_objects'] = $scopeSettings[static::NODE_KEY]['named_objects'];
            unset($scopeSettings[static::NODE_KEY]['named_objects']);
        }

        if (isset($scopeSettings[static::NODE_KEY]['named_queries'])) {
            $scopeSettings[static::NODE_KEY . '.named_queries'] = $scopeSettings[static::NODE_KEY]['named_queries'];
            unset($scopeSettings[static::NODE_KEY]['named_queries']);
        }
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer): void
    {
        $contextualizer->mapConfigArray(static::NODE_KEY . '.named_objects', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
        $contextualizer->mapConfigArray(static::NODE_KEY . '.named_queries', $config, ContextualizerInterface::MERGE_FROM_SECOND_LEVEL);
    }
}
