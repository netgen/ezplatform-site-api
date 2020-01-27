<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as CoreContentView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView as ContentViewParser;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionCollection;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryDefinitionMapper;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\RedirectConfiguration;
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
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider\ContentViewFallbackResolver
     */
    private $contentViewFallbackResolver;

    public function __construct(
        MatcherFactoryInterface $matcherFactory,
        QueryDefinitionMapper $queryDefinitionMapper,
        Resolver $redirectResolver,
        ContentViewFallbackResolver $contentViewFallbackResolver
    ) {
        $this->matcherFactory = $matcherFactory;
        $this->queryDefinitionMapper = $queryDefinitionMapper;
        $this->redirectResolver = $redirectResolver;
        $this->contentViewFallbackResolver = $contentViewFallbackResolver;
    }

    /**
     * {@inheritdoc}
     *
     * Returns view as a data transfer object.
     *
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\Exception\InvalidRedirectConfiguration
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getView(View $view): ?View
    {
        // Service is dispatched by the configured view class, so this should be safe
        /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view */
        $configHash = $this->matcherFactory->match($view);

        if ($configHash === null) {
            return $this->contentViewFallbackResolver->getEzPlatformFallbackDto($view);
        }

        // We can set the collection directly to the view, no need to go through DTO
        $view->addParameters([
            ContentView::QUERY_DEFINITION_COLLECTION_NAME => $this->getQueryDefinitionCollection($configHash, $view),
        ]);

        // Return DTO so that Configurator can set the data back to the $view
        return $this->getDTO($configHash, $view);
    }

    private function getQueryDefinitionCollection(array $configHash, ContentView $view): QueryDefinitionCollection
    {
        $queryDefinitionCollection = new QueryDefinitionCollection();
        $queriesConfiguration = $this->getQueriesConfiguration($configHash);

        foreach ($queriesConfiguration as $variableName => $queryConfiguration) {
            $queryDefinitionCollection->add(
                $variableName,
                $this->queryDefinitionMapper->map($queryConfiguration, $view)
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
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\Exception\InvalidRedirectConfiguration
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    private function getDTO(array $viewConfig, ContentView $view): CoreContentView
    {
        $dto = new CoreContentView();
        $dto->setConfigHash($viewConfig);

        $this->processRedirects($dto, $viewConfig, $view);

        if (isset($viewConfig['template'])) {
            $dto->setTemplateIdentifier($this->replaceTemplateIdentifierVariables($viewConfig['template'], $view));
        }

        if (isset($viewConfig['controller'])) {
            $dto->setControllerReference(new ControllerReference($viewConfig['controller']));
        }

        if (isset($viewConfig['params']) && \is_array($viewConfig['params'])) {
            $dto->addParameters($viewConfig['params']);
        }

        return $dto;
    }

    private function replaceTemplateIdentifierVariables(string $identifier, ContentView $view): string
    {
        $contentTypeIdentifier = $view->getSiteContent()->contentInfo->contentTypeIdentifier;

        return \preg_replace('/{content_type}/', $contentTypeIdentifier, $identifier) ?? $identifier;
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $dto
     * @param array $viewConfig
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\Exception\InvalidRedirectConfiguration
     */
    private function processRedirects(CoreContentView $dto, array $viewConfig, ContentView $view): void
    {
        if (!isset($viewConfig['redirect'])) {
            return;
        }

        $dto->setControllerReference(
            new ControllerReference(
                \sprintf('%s::%s', RedirectController::class, 'urlRedirectAction')
            )
        );

        $redirectConfig = RedirectConfiguration::fromConfigurationArray($viewConfig['redirect']);

        $dto->addParameters(
            [
                'path' => $this->redirectResolver->resolveTarget($redirectConfig, $view),
                'permanent' => $redirectConfig->isPermanent(),
            ]
        );
    }
}
