<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryExecutor;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Twig_Error_Runtime;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension for executing queries from the QueryCollection injected
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
            /**
             * @param $context
             * @param $name
             * @param bool $usePager
             *
             * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult|\Pagerfanta\Pagerfanta
             */
                'ng_query',
                function ($context, $name, $usePager = true) {
                    return $this->queryExecutor->execute(
                        $this->getQueryCollection($context)->getQueryDefinition($name),
                        $usePager
                    );
                },
                [
                    'needs_context' => true,
                ]
            ),
        ];
    }

    /**
     * Returns the QueryCollection from the given $context.
     *
     * @throws \Twig_Error_Runtime
     *
     * @param mixed $context
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollection
     */
    private function getQueryCollection($context)
    {
        $variableName = ContentView::QUERY_COLLECTION_NAME;

        if (is_array($context) && array_key_exists($variableName, $context)) {
            return $context['queryCollection'];
        }

        throw new Twig_Error_Runtime(
            "Could not find QueryCollection variable '{$variableName}'"
        );
    }
}
