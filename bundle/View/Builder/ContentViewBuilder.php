<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder;

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
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
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
     * @var \eZ\Publish\API\Repository\PermissionResolver
     */
    private $permissionResolver;

    public function __construct(
        Site $site,
        Repository $repository,
        Configurator $viewConfigurator,
        ParametersInjector $viewParametersInjector
    ) {
        $this->site = $site;
        $this->repository = $repository;
        $this->viewConfigurator = $viewConfigurator;
        $this->viewParametersInjector = $viewParametersInjector;
        $this->permissionResolver = $repository->getPermissionResolver();
    }

    public function matches($argument): bool
    {
        return is_string($argument) && strpos($argument, 'ng_content:') !== false;
    }

    /**
     * @param array $parameters
     *
     * @throws \Exception
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView
     */
    public function buildView(array $parameters): ContentView
    {
        $isEmbed = $this->isEmbedView($parameters);
        $viewType = $this->getViewType($parameters, $isEmbed);
        $maybeLocation = $this->getMaybeLocation($parameters);
        $content = $this->getContent($parameters, $isEmbed, $maybeLocation);

        $view = new ContentView($content, $maybeLocation, $isEmbed, $viewType);

        $this->viewParametersInjector->injectViewParameters($view, $parameters);
        $this->viewConfigurator->configure($view);

        return $view;
    }

    private function isEmbedView(array $parameters): bool
    {
        if ($parameters['_controller'] === 'ng_content:embedAction') {
            return true;
        }

        if (in_array($parameters['viewType'], ['embed', 'embed-inline'])) {
            return true;
        }

        return false;
    }

    private function getViewType(array $parameters, bool $isEmbed): string
    {
        $viewType = $parameters['viewType'];

        if ($isEmbed && $viewType === null) {
            return EmbedView::DEFAULT_VIEW_TYPE;
        }

        return $viewType;
    }

    /**
     * @param array $parameters
     *
     * @throws \Exception
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ResolveException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getMaybeLocation(array $parameters): ?Location
    {
        try {
            return $this->getProvidedLocation($parameters);
        } catch (ResolveException $e) {
            // do nothing
        }

        try {
            return $this->getLocationById($parameters);
        } catch (ResolveException $e) {
            // do nothing
        }

        return null;
    }

    /**
     * @param array $parameters
     *
     * @throws \Exception
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ResolveException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getProvidedLocation(array $parameters): Location
    {
        if (!isset($parameters['location'])) {
            throw new ResolveException('Location is not provided');
        }

        $location = $parameters['location'];

        if ($location instanceof APILocation) {
            return $this->sudoLoadLocation($location->id);
        }

        return $location;
    }

    /**
     * @param array $parameters
     *
     * @throws \Exception
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ResolveException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getLocationById(array $parameters): Location
    {
        if (isset($parameters['locationId'])) {
            return $this->sudoLoadVisibleLocation($parameters['locationId']);
        }

        throw new ResolveException('Could not resolve Location ID');
    }

    /**
     * @param array $parameters
     * @param bool $isEmbed
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location|null $maybeLocation
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function getContent(array $parameters, bool $isEmbed, ?Location $maybeLocation): Content
    {
        try {
            return $this->getProvidedContent($parameters);
        } catch (ResolveException $e) {
            // do nothing
        }

        try {
            return $this->getContentById($parameters, $isEmbed, $maybeLocation);
        } catch (ResolveException $e) {
            // do nothing
        }

        throw new InvalidArgumentException(
            'Content',
            'Content could not be loaded from the given parameters'
        );
    }

    /**
     * @param array $parameters
     *
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ResolveException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function getProvidedContent(array $parameters): Content
    {
        if (!isset($parameters['content'])) {
            throw new ResolveException('Content is not provided');
        }

        $content = $parameters['content'];

        if ($content instanceof APIContent) {
            return $this->loadContent($content->contentInfo->id);
        }

        return $content;
    }

    /**
     * @param array $parameters
     * @param bool $isEmbed
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location|null $maybeLocation
     *
     * @throws \Netgen\Bundle\EzPlatformSiteApiBundle\View\Builder\ResolveException
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function getContentById(array $parameters, bool $isEmbed, ?Location $maybeLocation): Content
    {
        $contentId = $this->getContentId($parameters, $maybeLocation);

        if ($isEmbed) {
            return $this->loadEmbeddedContent($contentId, $maybeLocation);
        }

        return $this->loadContent($contentId);
    }

    private function getContentId(array $parameters, ?Location $maybeLocation)
    {
        if (isset($parameters['contentId'])) {
            return $parameters['contentId'];
        }

        if (isset($maybeLocation)) {
            return $maybeLocation->contentInfo->id;
        }

        throw new ResolveException('Could not resolve Content ID');
    }

    /**
     * @param string|int $contentId
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location|null $maybeLocation
     *
     * @throws \Exception
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function loadEmbeddedContent($contentId, ?Location $maybeLocation): Content
    {
        $content = $this->sudoLoadContent($contentId);
        $versionInfo = $content->versionInfo;

        if (!$this->canEmbedContent($versionInfo->contentInfo, $maybeLocation)) {
            throw new UnauthorizedException(
                'content',
                'read|view_embed',
                [
                    'contentId' => $contentId,
                    'locationId' => $maybeLocation->id ?? null,
                ]
            );
        }

        if (!$versionInfo->isPublished() && !$this->canReadVersion($versionInfo)) {
            throw new UnauthorizedException(
                'content',
                'versionread',
                [
                    'contentId' => $contentId,
                    'versionNo' => $versionInfo->versionNo,
                ]
            );
        }

        return $content;
    }

    /**
     * @param string|int $contentId
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
     * @param string|int $contentId
     *
     * @throws \Exception
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function sudoLoadContent($contentId): Content
    {
        return $this->repository->sudo(
            function () use ($contentId): Content {
                return $this->site->getLoadService()->loadContent($contentId);
            }
        );
    }

    /**
     * @todo Do we need to handle permissions here ?
     *
     * @param string|int $locationId
     *
     * @throws \Exception
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function sudoLoadLocation($locationId): Location
    {
        return $this->repository->sudo(
            function () use ($locationId): Location {
                return $this->site->getLoadService()->loadLocation($locationId);
            }
        );
    }

    /**
     * @param string|int $locationId
     *
     * @throws \Exception
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function sudoLoadVisibleLocation($locationId): Location
    {
        $location = $this->sudoLoadLocation($locationId);

        if ($location->innerLocation->invisible) {
            throw new NotFoundHttpException(
                'Location cannot be viewed because it is flagged as invisible.'
            );
        }

        return $location;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param \Netgen\EzPlatformSiteApi\API\Values\Location $maybeLocation
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return bool
     */
    private function canEmbedContent(ContentInfo $contentInfo, ?Location $maybeLocation): bool
    {
        $targets = isset($maybeLocation) ? [$maybeLocation->innerLocation] : [];

        return
            $this->permissionResolver->canUser('content', 'read', $contentInfo, $targets) ||
            $this->permissionResolver->canUser('content', 'view_embed', $contentInfo, $targets);

    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return bool
     */
    private function canReadVersion(VersionInfo $versionInfo): bool
    {
        return $this->permissionResolver->canUser('content', 'versionread', $versionInfo);
    }
}
