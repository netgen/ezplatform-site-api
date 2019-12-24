<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Core\FieldType\XmlText;

use DOMDocument;
use eZ\Publish\API\Repository\Exceptions\NotFoundException as APINotFoundException;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\FieldType\XmlText\Converter\EmbedToHtml5 as BaseEmbedToHtml5;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use LogicException;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Traits\SiteAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Converts embedded elements from internal XmlText representation to HTML5.
 *
 * Overrides the built in converter to allow rendering embedded content with Site API controller.
 */
class RenderEmbedConverter extends BaseEmbedToHtml5
{
    use SiteAwareTrait;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ContentViewBuilder
     */
    private $viewBuilder;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer
     */
    private $viewRenderer;

    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    public function setViewBuilder(ContentViewBuilder $viewBuilder): void
    {
        $this->viewBuilder = $viewBuilder;
    }

    public function setViewRenderer(ViewRenderer $viewRenderer): void
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     * @throws \Exception
     */
    protected function processTag(DOMDocument $xmlDoc, $tagName): void
    {
        $this->logger = $this->logger ?: new NullLogger();
        $permissionResolver = $this->repository->getPermissionResolver();

        /** @var \DOMElement $embed */
        foreach ($xmlDoc->getElementsByTagName($tagName) as $embed) {
            if (!$view = $embed->getAttribute('view')) {
                $view = $tagName;
            }

            $embedContent = null;
            $parameters = $this->getParameters($embed);

            $contentId = $embed->getAttribute('object_id');
            $locationId = $embed->getAttribute('node_id');

            if ($contentId) {
                try {
                    /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
                    $content = $this->repository->sudo(
                        function () use ($contentId): Content {
                            return $this->site->getLoadService()->loadContent($contentId);
                        }
                    );

                    if (
                        !$permissionResolver->canUser('content', 'read', $content->innerContent)
                        && !$permissionResolver->canUser('content', 'view_embed', $content->innerContent)
                    ) {
                        throw new UnauthorizedException('content', 'read', ['contentId' => $contentId]);
                    }

                    // Check published status of the Content
                    if (
                        $content->versionInfo->status !== APIVersionInfo::STATUS_PUBLISHED
                        && !$permissionResolver->canUser('content', 'versionread', $content->innerContent)
                    ) {
                        throw new UnauthorizedException('content', 'versionread', ['contentId' => $contentId]);
                    }

                    $embedContent = $this->renderContentEmbed($content, $view, $parameters);
                } catch (APINotFoundException $e) {
                    $this->logger->error(
                        \sprintf('While generating embed for xmltext, could not locate content with ID %d', $contentId)
                    );
                }
            } elseif ($locationId) {
                try {
                    /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
                    $location = $this->repository->sudo(
                        function () use ($locationId): Location {
                            return $this->site->getLoadService()->loadLocation($locationId);
                        }
                    );

                    if (
                        !$permissionResolver->canUser('content', 'read', $location->contentInfo->innerContentInfo, [$location->innerLocation])
                        && !$permissionResolver->canUser('content', 'view_embed', $location->contentInfo->innerContentInfo, [$location->innerLocation])
                    ) {
                        throw new UnauthorizedException('content', 'read', ['locationId' => $location->id]);
                    }

                    $embedContent = $this->renderLocationEmbed($location, $view, $parameters);
                } catch (APINotFoundException $e) {
                    $this->logger->error(
                        \sprintf('While generating embed for xmltext, could not locate location with ID %d', $locationId)
                    );
                }
            }

            if ($embedContent === null) {
                // Remove empty embed
                $embed->parentNode->removeChild($embed);
            } else {
                $embed->appendChild($xmlDoc->createCDATASection($embedContent));
            }
        }
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Values\Content $content
     * @param string $viewType
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    private function renderContentEmbed(Content $content, string $viewType, array $parameters): string
    {
        if ($this->configResolver->getParameter('ng_xmltext_embed_without_subrequest') === true) {
            return $this->renderContentEmbedWithoutSubrequest($content, $viewType, $parameters);
        }

        return (string)$this->fragmentHandler->render(
            new ControllerReference(
                'ng_content:embedAction',
                [
                    'content' => $content,
                    'viewType' => $viewType,
                    'layout' => false,
                    'params' => $parameters,
                ]
            )
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Values\Content $content
     * @param string $viewType
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    private function renderContentEmbedWithoutSubrequest(Content $content, string $viewType, array $parameters): string
    {
        $baseParameters = [
            'content' => $content,
            'viewType' => $viewType,
            'layout' => false,
            '_controller' => 'ng_content:embedAction',
        ];

        $parameters = $baseParameters + $parameters;
        $view = $this->viewBuilder->buildView($parameters);

        if (!$this->viewMatched($view)) {
            throw new LogicException(
                \sprintf('Could not match view "%s" for Content #%d', $viewType, $content->id)
            );
        }

        return $this->viewRenderer->render($view, $parameters, false);
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location $location
     * @param string $view
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    private function renderLocationEmbed(Location $location, string $view, array $parameters): string
    {
        if ($this->configResolver->getParameter('ng_xmltext_embed_without_subrequest') === true) {
            return $this->renderLocationEmbedWithoutSubrequest($location, $view, $parameters);
        }

        return (string)$this->fragmentHandler->render(
            new ControllerReference(
                'ng_content:embedAction',
                [
                    'content' => $location->content,
                    'location' => $location,
                    'viewType' => $view,
                    'layout' => false,
                    'params' => $parameters,
                ]
            )
        );
    }

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location $location
     * @param string $viewType
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return string
     */
    private function renderLocationEmbedWithoutSubrequest(Location $location, string $viewType, array $parameters): string
    {
        $baseParameters = [
            'content' => $location->content,
            'location' => $location,
            'viewType' => $viewType,
            'layout' => false,
            '_controller' => 'ng_content:embedAction',
        ];

        $parameters = $baseParameters + $parameters;
        $view = $this->viewBuilder->buildView($parameters);

        if (!$this->viewMatched($view)) {
            throw new LogicException(
                \sprintf('Could not match view "%s" for Content #%d', $viewType, $location->content->id)
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
