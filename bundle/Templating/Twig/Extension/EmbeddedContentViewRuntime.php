<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use LogicException;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer;

/**
 * Twig extension runtime for Site API embedded content view rendering.
 */
class EmbeddedContentViewRuntime
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder
     */
    private $viewBuilder;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer
     */
    private $viewRenderer;

    public function __construct(
        ContentViewBuilder $viewBuilder,
        ViewRenderer $viewRenderer
    ) {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Renders the HTML for a given $content.
     *
     * @param string $viewType
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    public function renderEmbeddedContentView(string $viewType, array $parameters = []): string
    {
        $baseParameters = [
            'viewType' => $viewType,
            'layout' => false,
            '_controller' => 'ng_content:embedAction',
        ];

        $view = $this->viewBuilder->buildView($baseParameters + $parameters);

        if (!$this->viewMatched($view)) {
            throw new LogicException(
                \sprintf(
                    'Could not match embed view "%s" for Content #%d',
                    $viewType,
                    $view->getSiteContent()->id
                )
            );
        }

        return $this->viewRenderer->render($view, $parameters, false);
    }

    /**
     * This is the only way to check if the view actually matched?
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $contentView
     *
     * @return bool
     */
    private function viewMatched(ContentView $contentView): bool
    {
        return !empty($contentView->getConfigHash());
    }
}
