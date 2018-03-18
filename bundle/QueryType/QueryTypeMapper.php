<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * todo
 */
final class QueryTypeMapper
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser
     */
    private $sortClauseParser;

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\Search\SortClauseParser $sortClauseParser
     */
    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        SortClauseParser $sortClauseParser
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->sortClauseParser = $sortClauseParser;
    }

    /**
     * Map given $view to the query collection.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition $queryDefinition
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query|\eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    public function map(ContentView $view, QueryDefinition $queryDefinition)
    {
        $options = $queryDefinition->options;
        $queryTypeName = $options['query_type'];
        $queryType = $this->queryTypeRegistry->getQueryType($queryTypeName);
        $parameters = $this->extractParametersFromOptions($view, $options);

        return $queryType->getQuery($parameters);
    }

    /**
     * Extract parameters from the given array of $options.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     * @param array $options
     *
     * @return array
     */
    private function extractParametersFromOptions(ContentView $contentView, array $options)
    {
        $parameters = [];

        if (isset($options['parameters'])) {
            foreach ($options['parameters'] as $name => $value) {
                $parameters[$name] = $this->extractParameters($contentView, $value);
            }
        }

        return $parameters;
    }

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     * @param array $parameterValue
     *
     * @return array|string
     */
    private function extractParameters(ContentView $contentView, $parameterValue)
    {
        if (is_array($parameterValue)) {
            $queryParameters = [];

            foreach ($parameterValue as $name => $value) {
                $queryParameters[$name] = $this->extractParameters($contentView, $value);
            }

            return $queryParameters;
        }

        return $this->processValue($contentView, $parameterValue);
    }

    /**
     * todo
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     * @param mixed $value
     *
     * @return mixed
     */
    private function processValue(ContentView $contentView, $value)
    {
        if (is_string($value) && 0 === strpos($value, '@=')) {
            $language = new ExpressionLanguage();

            return $language->evaluate(
                substr($value, 2),
                [
                    'view' => $contentView,
                    'location' => $contentView->getSiteLocation(),
                    'content' => $contentView->getSiteContent(),
                    'sort' => $this->sortClauseParser,
                ]
            );
        }

        return $value;
    }
}
