<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Twig_Error_Runtime;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension for executing queries from the QueryDefinitionCollection injected
 * into the template.
 */
class QueryExtension extends Twig_Extension
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor
     */
    private $queryExecutor;

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor $queryExecutor
     */
    public function __construct(QueryExecutor $queryExecutor)
    {
        $this->queryExecutor = $queryExecutor;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'ng_query',
                /**
                 * @param mixed $context
                 * @param string $name
                 *
                 * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
                 */
                function ($context, $name) {
                    return $this->queryExecutor->execute(
                        $this->getQueryDefinitionCollection($context)->get($name),
                        true
                    );
                },
                [
                    'needs_context' => true,
                ]
            ),
            new Twig_SimpleFunction(
                'ng_raw_query',
                /**
                 * @param mixed $context
                 * @param string $name
                 *
                 * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
                 */
                function ($context, $name) {
                    return $this->queryExecutor->execute(
                        $this->getQueryDefinitionCollection($context)->get($name),
                        false
                    );
                },
                [
                    'needs_context' => true,
                ]
            ),
        ];
    }

    /**
     * Returns the QueryDefinitionCollection variable from the given $context.
     *
     * @throws \Twig_Error_Runtime
     *
     * @param mixed $context
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionCollection
     */
    private function getQueryDefinitionCollection($context)
    {
        $variableName = ContentView::QUERY_DEFINITION_COLLECTION_NAME;

        if (is_array($context) && array_key_exists($variableName, $context)) {
            return $context[$variableName];
        }

        throw new Twig_Error_Runtime(
            "Could not find QueryDefinitionCollection variable '{$variableName}'"
        );
    }
}
