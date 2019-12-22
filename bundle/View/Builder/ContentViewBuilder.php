<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\Core\MVC\Symfony\View\Builder\ViewBuilder;
use eZ\Publish\Core\MVC\Symfony\View\Configurator;
use eZ\Publish\Core\MVC\Symfony\View\EmbedView;
use eZ\Publish\Core\MVC\Symfony\View\ParametersInjector;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationResolver;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Builds ContentView objects.
 */
class ContentViewBuilder implements ViewBuilder
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\View\Configurator
     */
    private $viewConfigurator;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\View\ParametersInjector
     */
    private $viewParametersInjector;

    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\LocationResolver
     */
    private $locationResolver;

    public function __construct(
        Site $site,
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector,
        LocationResolver $locationResolver
    ) {
        $this->site = $site;
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->locationResolver = $locationResolver;
    }

    public function matches($argument): bool
    {
        return \is_string($argument) && \strpos($argument, 'ng_content:') !== false;
    }

    /**
     * @param array $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Exception
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function buildView(array $parameters): ContentView
    {
        $view = new ContentView(null, [], $parameters['viewType']);
        $view->setIsEmbed($this->isEmbed($parameters));

        if ($parameters['viewType'] === null && $view->isEmbed()) {
            $view->setViewType(EmbedView::DEFAULT_VIEW_TYPE);
        }

        if (isset($parameters['locationId'])) {
            $location = $this->loadLocation($parameters['locationId']);
        } elseif (isset($parameters['location'])) {
            $location = $parameters['location'];
            if (!$location instanceof Location && $location instanceof APILocation) {
                $location = $this->loadLocation($location->id, false);
            }
        } else {
            $location = null;
        }

        if (isset($parameters['content'])) {
            $content = $parameters['content'];
            if (!$content instanceof Content && $content instanceof APIContent) {
                $content = $this->loadContent($content->contentInfo->id);
            }
        } else {
            if (isset($parameters['contentId'])) {
                $contentId = $parameters['contentId'];
            } elseif (isset($location)) {
                $contentId = $location->contentInfo->id;
            } else {
                throw new InvalidArgumentException(
                    'Content',
                    'No content could be loaded from parameters'
                );
            }

            $content = $view->isEmbed() ?
                $this->loadEmbeddedContent($contentId, $location) :
                $this->loadContent($contentId);
        }

        $view->setSiteContent($content);

        if ($location === null) {
            try {
                $location = $this->locationResolver->getLocation($content);
            } catch (NotFoundException $e) {
                // do nothing
            }
        }

        if (isset($location)) {
            if ($location->contentInfo->id !== $content->id) {
                throw new InvalidArgumentException(
                    'Location',
                    'Provided location does not belong to selected content'
                );
            }

            $view->setSiteLocation($location);
        }

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    /**
     * Loads Content with id $contentId.
     *
     * @param int|string $contentId
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function loadContent($contentId): Content
    {
        return $this->site->getLoadService()->loadContent($contentId);
    }

    /**
     * Loads the embedded content with id $contentId.
     * Will load the content with sudo(), and check if the user can view_embed this content, for the given location
     * if provided.
     *
     * @param int|string $contentId
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location $location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Exception
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function loadEmbeddedContent($contentId, Location $location = null): Content
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $this->repository->sudo(
            function () use ($contentId): Content {
                return $this->site->getLoadService()->loadContent($contentId);
            }
        );

        $versionInfo = $content->versionInfo;

        if (!$this->canReadOrViewEmbed($versionInfo->contentInfo, $location)) {
            throw new UnauthorizedException(
                'content',
                'read|view_embed',
                ['contentId' => $contentId, 'locationId' => $location !== null ? $location->id : 'n/a']
            );
        }

        // Check that Content is published, since sudo allows loading unpublished content.
        if (
            $versionInfo->status !== VersionInfo::STATUS_PUBLISHED
            && !$this->repository->getPermissionResolver()->canUser('content', 'versionread', $versionInfo)
        ) {
            throw new UnauthorizedException('content', 'versionread', ['contentId' => $contentId]);
        }

        return $content;
    }

    /**
     * Loads a visible Location.
     *
     * @todo Do we need to handle permissions here ?
     *
     * @param int|string $locationId
     * @param bool $checkVisibility
     *
     * @throws \Exception
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function loadLocation($locationId, bool $checkVisibility = true): Location
    {
        $location = $this->repository->sudo(
            function (Repository $repository) use ($locationId): Location {
                return $this->site->getLoadService()->loadLocation($locationId);
            }
        );

        if ($checkVisibility && $location->innerLocation->invisible) {
            throw new NotFoundHttpException(
                'Location cannot be displayed as it is flagged as invisible.'
            );
        }

        return $location;
    }

    /**
     * Checks if a user can read a content, or view it as an embed.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location $location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return bool
     */
    private function canReadOrViewEmbed(ContentInfo $contentInfo, Location $location = null): bool
    {
        $targets = isset($location) ? [$location->innerLocation] : [];

        return
            $this->repository->getPermissionResolver()->canUser('content', 'read', $contentInfo, $targets) ||
            $this->repository->getPermissionResolver()->canUser('content', 'view_embed', $contentInfo, $targets);
    }

    /**
     * Checks if the view is an embed one.
     * Uses either the controller action (embedAction), or the viewType (embed/embed-inline).
     *
     * @param array $parameters The ViewBuilder parameters array
     *
     * @return bool
     */
    private function isEmbed(array $parameters): bool
    {
        if ($parameters['_controller'] === 'ng_content:embedAction') {
            return true;
        }

        if (\in_array($parameters['viewType'], ['embed', 'embed-inline'], true)) {
            return true;
        }

        return false;
    }
}
