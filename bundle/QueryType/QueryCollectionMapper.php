<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\FilterService;
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
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\FilterService
     */
    private $filterService;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser
     */
    private $sortClauseParser;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\EzPlatformSiteApi\Core\Site\FilterService $filterService
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser $sortClauseParser
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        FilterService $filterService,
        SortClauseParser $sortClauseParser,
        RequestStack $requestStack
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->filterService = $filterService;
        $this->sortClauseParser = $sortClauseParser;
        $this->requestStack = $requestStack;
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
        $queryCollection = new QueryCollection(
            $this->queryTypeRegistry,
            $this->filterService
        );

        foreach ($configuration as $name => $queryConfiguration) {
            $queryDefinition = new QueryDefinition([
                'name' => $queryConfiguration['query_type'],
                'parameters' => $this->resolveParameters($queryConfiguration['parameters'], $view),
            ]);
            $queryCollection->addQueryDefinition($name, $queryDefinition);
        }

        return $queryCollection;
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
}
