<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView as CoreContentView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use eZ\Publish\Core\MVC\Symfony\View\ViewProvider;
use Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser\ContentView as ContentViewParser;
use Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
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
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper
     */
    private $queryCollectionMapper;

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface $matcherFactory
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\QueryCollectionMapper $queryCollectionMapper
     */
    public function __construct(
        MatcherFactoryInterface $matcherFactory,
        QueryCollectionMapper $queryCollectionMapper
    ) {
        $this->matcherFactory = $matcherFactory;
        $this->queryCollectionMapper = $queryCollectionMapper;
    }

    /**
     * @inheritdoc
     *
     * Returns view as a data transfer object.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function getView(View $view)
    {
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return null;
        }

        // We can set the collection directly to the view, no need to go through DTO
        $view->addParameters([
            ContentView::QUERY_COLLECTION_NAME => $this->queryCollectionMapper->map(
                $this->getQueryConfiguration($configHash),
                // Service is dispatched by the configured view class, so this should be safe
                $view
            ),
        ]);

        // Return DTO so that Configurator can set the data back to the $view
        return $this->getDTO($configHash);
    }

    private function getQueryConfiguration(array $configHash)
    {
        if (array_key_exists(ContentViewParser::QUERY_KEY, $configHash)) {
            return $configHash[ContentViewParser::QUERY_KEY];
        }

        return [];
    }

    /**
     * Builds a ContentView object from $viewConfig.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @param array $viewConfig
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    private function getDTO(array $viewConfig)
    {
        $dto = new CoreContentView();
        $dto->setConfigHash($viewConfig);

        if (isset($viewConfig['template'])) {
            $dto->setTemplateIdentifier($viewConfig['template']);
        }

        if (isset($viewConfig['controller'])) {
            $dto->setControllerReference(new ControllerReference($viewConfig['controller']));
        }

        if (isset($viewConfig['params']) && is_array($viewConfig['params'])) {
            $dto->addParameters($viewConfig['params']);
        }

        return $dto;
    }
}
