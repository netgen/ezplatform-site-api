<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\ValueObject;
use LogicException;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

/**
 * Twig extension runtime for Site API content view rendering.
 */
class ContentViewRuntime
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder
     */
    private $viewBuilder;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer
     */
    private $viewRenderer;

    public function __construct(ContentViewBuilder $viewBuilder, ViewRenderer $viewRenderer)
    {
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Renders the HTML for a given $content.
     *
     * @param \eZ\Publish\API\Repository\Values\ValueObject $value
     * @param string $viewType
     * @param array $parameters
     * @param bool $layout
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    public function renderContentView(
        ValueObject $value,
        string $viewType,
        array $parameters = [],
        bool $layout = false
    ): string {
        /** @var APIContent|Content $content */
        $content = $this->getContent($value);
        $location = $this->getLocation($value);

        $baseParameters = [
            'content' => $content,
            'viewType' => $viewType,
            'layout' => $layout,
            '_controller' => 'ng_content:viewAction',
        ];

        if ($location !== null) {
            $baseParameters['location'] = $location;
        }

        $view = $this->viewBuilder->buildView($baseParameters + $parameters);

        return $this->viewRenderer->render($view, $parameters, $layout);
    }

    private function getContent(ValueObject $value): ValueObject
    {
        if ($value instanceof Content || $value instanceof APIContent) {
            return $value;
        }

        if ($value instanceof Location || $value instanceof APILocation) {
            // eZ location also has a lazy loaded "content" property
            return $value->content;
        }

        throw new LogicException('Given value must be Content or Location instance');
    }

    private function getLocation(ValueObject $value): ?ValueObject
    {
        if ($value instanceof Location || $value instanceof APILocation) {
            return $value;
        }

        return null;
    }
}
