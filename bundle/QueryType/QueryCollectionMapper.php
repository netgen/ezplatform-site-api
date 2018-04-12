<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @internal
 *
 * QueryCollectionMapper maps query configuration from content view to a QueryCollection instance.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollection
 */
final class QueryCollectionMapper
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser
     */
    private $sortClauseParser;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $queriesConfig;

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser $sortClauseParser
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param array $queriesConfig
     */
    public function __construct(
        SortClauseParser $sortClauseParser,
        RequestStack $requestStack,
        array $queriesConfig
    ) {
        $this->sortClauseParser = $sortClauseParser;
        $this->requestStack = $requestStack;
        $this->queriesConfig = $queriesConfig;
    }

    /**
     * Map given query $configuration to a QueryCollection instance.
     *
     * @param array $configuration
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view Needed to resolve configured language expressions.
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollection
     */
    public function map(array $configuration, ContentView $view)
    {
        $queryCollection = new QueryCollection();

        foreach ($configuration as $variableName => $queryConfiguration) {
            $queryCollection->addQueryDefinition(
                $variableName,
                $this->getQueryDefinition($queryConfiguration, $view)
            );
        }

        return $queryCollection;
    }

    private function getQueryDefinition(array $config, ContentView $view)
    {
        if (isset($config['named_query'])) {
            $queryName = $config['named_query'];

            return $this->buildQueryDefinition($this->queriesConfig[$queryName], $view);
        }

        return $this->buildQueryDefinition($config, $view);
    }

    private function buildQueryDefinition(array $config, ContentView $view)
    {
        return new QueryDefinition([
            'name' => $config['query_type'],
            'parameters' => $this->resolveParameters($config['parameters'], $view),
            'useFilter' => $this->resolveParameters($config['use_filter'], $view),
            'maxPerPage' => $this->resolveParameter($config['max_per_page'], $view),
            'page' => $this->resolveParameter($config['page'], $view),
        ]);
    }

    /**
     * Recursively process given $parameters, resolving usage of ExpressionLanguage if needed.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     * @param mixed $parameters
     *
     * @return array|string
     */
    private function resolveParameters($parameters, ContentView $contentView)
    {
        if (is_array($parameters)) {
            $processedParameters = [];

            foreach ($parameters as $name => $subParameters) {
                $processedParameters[$name] = $this->resolveParameters($subParameters, $contentView);
            }

            return $processedParameters;
        }

        return $this->resolveParameter($parameters, $contentView);
    }

    /**
     * Resolve given $value through ExpressionLanguage if needed.
     *
     * @param mixed $value
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     *
     * @return mixed
     */
    private function resolveParameter($value, ContentView $contentView)
    {
        if (is_string($value) && 0 === strpos($value, '@=')) {
            $language = new ExpressionLanguage();

            $this->registerFunctions($language);

            return $language->evaluate(
                substr($value, 2),
                [
                    'view' => $contentView,
                    'location' => $contentView->getSiteLocation(),
                    'content' => $contentView->getSiteContent(),
                    'sort' => $this->sortClauseParser,
                    'request' => $this->requestStack->getCurrentRequest(),
                ]
            );
        }

        return $value;
    }

    private function registerFunctions(ExpressionLanguage $language)
    {
        $language->register(
            'viewParam',
            function ($name, $default) {
                return sprintf('($view->hasParameter(%1$s) ? $view->getParameter(%1$s) : %2$s)', $name, $default);
            },
            function ($arguments, $name, $default) {
                /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view */
                $view = $arguments['view'];

                if ($view->hasParameter($name)) {
                    return $view->getParameter($name);
                }

                return $default;
            }
        );

        $language->register(
            'queryParam',
            function ($name, $default) {
                return sprintf('($request->query->get(%1$s, %2$s))', $name, $default);
            },
            function ($arguments, $name, $default) {
                /** @var \Symfony\Component\HttpFoundation\Request $request */
                $request = $arguments['request'];

                return $request->query->get($name, $default);
            }
        );
    }
}
