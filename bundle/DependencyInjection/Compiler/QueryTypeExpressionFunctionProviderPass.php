<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function array_keys;

final class QueryTypeExpressionFunctionProviderPass implements CompilerPassInterface
{
    private const QueryTypeExpressionLanguageId = 'netgen.ezplatform_site.query_type.expression_language';
    private const QueryTypeExpressionFunctionProviderTag = 'netgen.ezplatform_site.query_type.expression_function_provider';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::QueryTypeExpressionLanguageId)) {
            return;
        }

        $expressionLanguageDefinition = $container->getDefinition(self::QueryTypeExpressionLanguageId);
        $functionProviders = $container->findTaggedServiceIds(self::QueryTypeExpressionFunctionProviderTag);

        foreach (array_keys($functionProviders) as $functionProviderId) {
            $expressionLanguageDefinition->addMethodCall(
                'registerProvider',
                [$functionProviderId]
            );
        }
    }
}
