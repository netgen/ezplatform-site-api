<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as CoreContentView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView as ContentViewParser;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionCollection;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionMapper;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\Resolver;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * A kind of a plugin to the Configurator, uses view configuration.
 *
 * @see \eZ\Publish\Core\MVC\Symfony\View\Configurator\ViewProvider
 */
class Configured implements ViewProvider
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface
     */
    protected $matcherFactory;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionMapper
     */
    private $queryDefinitionMapper;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\Resolver
     */
    private $redirectResolver;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface $matcherFactory
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionMapper $queryDefinitionMapper
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\Resolver
     */
    public function __construct(
        MatcherFactoryInterface $matcherFactory,
        QueryDefinitionMapper $queryDefinitionMapper,
        Resolver $redirectResolver
    ) {
        $this->matcherFactory = $matcherFactory;
        $this->queryDefinitionMapper = $queryDefinitionMapper;
        $this->redirectResolver = $redirectResolver;
    }

    /**
     * {@inheritdoc}
     *
     * Returns view as a data transfer object.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function getView(View $view): ?View
    {
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return null;
        }

        // We can set the collection directly to the view, no need to go through DTO
        $view->addParameters([
            ContentView::QUERY_DEFINITION_COLLECTION_NAME => $this->getQueryDefinitionCollection($configHash, $view),
        ]);

        // Return DTO so that Configurator can set the data back to the $view
        return $this->getDTO($configHash, $view);
    }

    private function getQueryDefinitionCollection(array $configHash, View $view): QueryDefinitionCollection
    {
        $queryDefinitionCollection = new QueryDefinitionCollection();
        $queriesConfiguration = $this->getQueriesConfiguration($configHash);

        foreach ($queriesConfiguration as $variableName => $queryConfiguration) {
            $queryDefinitionCollection->add(
                $variableName,
                $this->queryDefinitionMapper->map(
                    $queryConfiguration,
                    // Service is dispatched by the configured view class, so this should be safe
                    /* @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view */
                    $view
                )
            );
        }

        return $queryDefinitionCollection;
    }

    private function getQueriesConfiguration(array $configHash): array
    {
        if (\array_key_exists(ContentViewParser::QUERY_KEY, $configHash)) {
            return $configHash[ContentViewParser::QUERY_KEY];
        }

        return [];
    }

    /**
     * Builds a ContentView object from $viewConfig.
     *
     * @param array $viewConfig
     * @param ContentView $view
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    private function getDTO(array $viewConfig, ContentView $view): CoreContentView
    {
        $dto = new CoreContentView();
        $dto->setConfigHash($viewConfig);

        if (isset($viewConfig['permanent_redirect']) || isset($viewConfig['temporary_redirect'])) {
            $dto->setControllerReference(
                new ControllerReference(
                    'Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction'
                )
            );

            $config = isset($viewConfig['permanent_redirect']) ? $viewConfig['permanent_redirect'] : $viewConfig['temporary_redirect'];
            $path = $this->redirectResolver->resolveTarget($config, $view);

            $dto->addParameters(
                [
                    'path' => $path,
                    'permanent' => isset($viewConfig['permanent_redirect'])
                ]
            );
        }

        if (isset($viewConfig['template'])) {
            $dto->setTemplateIdentifier($viewConfig['template']);
        }

        if (isset($viewConfig['controller'])) {
            $dto->setControllerReference(new ControllerReference($viewConfig['controller']));
        }

        if (isset($viewConfig['params']) && \is_array($viewConfig['params'])) {
            $dto->addParameters($viewConfig['params']);
        }

        return $dto;
    }
}
