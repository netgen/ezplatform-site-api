<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\QueryType;

use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use InvalidArgumentException;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\QueryType as SiteQueryType;

/**
 * QueryDefinitionMapper maps query configuration to a QueryDefinition instance.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition
 *
 * @internal Do not depend on this service, it can be changed without warning.
 */
final class QueryDefinitionMapper
{
    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor
     */
    private $parameterProcessor;

    /**
     * @var array
     */
    private $namedQueriesConfiguration;

    /**
     * @param \eZ\Publish\Core\QueryType\QueryTypeRegistry $queryTypeRegistry
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor $parameterProcessor
     * @param array $namedQueriesConfiguration
     */
    public function __construct(
        QueryTypeRegistry $queryTypeRegistry,
        ParameterProcessor $parameterProcessor,
        array $namedQueriesConfiguration
    ) {
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->parameterProcessor = $parameterProcessor;
        $this->namedQueriesConfiguration = $namedQueriesConfiguration;
    }

    /**
     * Map given $configuration in $view context to a QueryDefinition instance.
     *
     * @param array $configuration
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @throws \InvalidArgumentException
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition
     */
    public function map(array $configuration, ContentView $view)
    {
        if (isset($configuration['named_query'])) {
            $namedQueryConfiguration = $this->getNamedQueryConfiguration($configuration['named_query']);
            $configuration = $this->overrideConfiguration($namedQueryConfiguration, $configuration);
        }

        return $this->buildQueryDefinition($configuration, $view);
    }

    /**
     * Override $configuration parameters with $override.
     *
     * @param array $configuration
     * @param array $override
     *
     * @return array
     */
    private function overrideConfiguration(array $configuration, array $override)
    {
        return array_replace_recursive($configuration, $override);
    }

    /**
     * Return named query configuration by the given $name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException If no such configuration exist.
     *
     * @return array
     */
    private function getNamedQueryConfiguration($name)
    {
        if (array_key_exists($name, $this->namedQueriesConfiguration)) {
            return $this->namedQueriesConfiguration[$name];
        }

        throw new InvalidArgumentException(
            "Could not find query configuration named '{$name}'"
        );
    }

    /**
     * Build QueryDefinition instance from the given arguments.
     *
     * @param array $configuration
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinition
     */
    private function buildQueryDefinition(array $configuration, ContentView $view)
    {
        $parameters = $this->processParameters($configuration['parameters'], $view);

        $this->injectSupportedParameters($parameters, $configuration['query_type'], $view);

        return new QueryDefinition([
            'name' => $configuration['query_type'],
            'parameters' => $parameters,
            'useFilter' => $this->parameterProcessor->process($configuration['use_filter'], $view),
            'maxPerPage' => $this->parameterProcessor->process($configuration['max_per_page'], $view),
            'page' => $this->parameterProcessor->process($configuration['page'], $view),
        ]);
    }

    /**
     * Inject parameters into $parameters if available in the $view and supported by the QueryType.
     *
     * @param array $parameters
     * @param string $queryTypeName
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     */
    private function injectSupportedParameters(array &$parameters, $queryTypeName, ContentView $view)
    {
        $queryType = $this->queryTypeRegistry->getQueryType($queryTypeName);

        if (!$queryType instanceof SiteQueryType) {
            return;
        }

        if (!array_key_exists('content', $parameters) && $queryType->supportsParameter('content')) {
            $parameters['content'] = $view->getSiteContent();
        }

        if (!array_key_exists('location', $parameters) && $queryType->supportsParameter('location')) {
            $parameters['location'] = $view->getSiteLocation();
        }
    }

    /**
     * Recursively process given $parameters using ParameterProcessor.
     *
     * @see \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor
     *
     * @param mixed $parameters
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return array|string
     */
    private function processParameters($parameters, ContentView $view)
    {
        if (!is_array($parameters)) {
            return $this->parameterProcessor->process($parameters, $view);
        }

        $processedParameters = [];

        foreach ($parameters as $name => $subParameters) {
            $processedParameters[$name] = $this->processParameters($subParameters, $view);
        }

        return $processedParameters;
    }
}
