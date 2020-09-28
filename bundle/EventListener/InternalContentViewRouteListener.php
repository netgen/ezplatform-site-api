<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use function class_exists;
use function in_array;

class InternalContentViewRouteListener implements EventSubscriberInterface
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    private $fragmentHandler;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $siteaccessGroups;

    public function __construct(
        ConfigResolverInterface $configResolver,
        FragmentHandler $fragmentHandler,
        RouterInterface $router,
        array $siteaccessGroups
    ) {
        $this->configResolver = $configResolver;
        $this->fragmentHandler = $fragmentHandler;
        $this->router = $router;
        $this->siteaccessGroups = $siteaccessGroups;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isInternalContentViewRoute($request)) {
            return;
        }

        $siteaccess = $request->attributes->get('siteaccess');

        if (!$siteaccess instanceof SiteAccess || $this->isAdminSiteaccess($siteaccess)) {
            return;
        }

        if (!$this->configResolver->getParameter('ng_site_api.enable_internal_view_route')) {
            throw new NotFoundHttpException();
        }

        $event->setResponse($this->getResponse($request));
    }

    private function getResponse(Request $request): Response
    {
        if ($this->configResolver->getParameter('ng_site_api.redirect_internal_view_route_to_url_alias')) {
            return new RedirectResponse($this->generateUrlAlias($request));
        }

        return new Response($this->renderView($request));
    }

    private function renderView(Request $request): string
    {
        $attributes = [
            'contentId' => $request->attributes->getInt('contentId'),
            'layout' => $request->attributes->getBoolean('layout', true),
            'viewType' => 'full',
        ];

        $locationId = $request->attributes->get('locationId');

        if ($locationId !== null) {
            $attributes['locationId'] = (int) $locationId;
        }

        return $this->fragmentHandler->render(
            new ControllerReference('ng_content:viewAction', $attributes)
        );
    }

    private function isInternalContentViewRoute(Request $request): bool
    {
        return $request->attributes->get('_route') === UrlAliasGenerator::INTERNAL_CONTENT_VIEW_ROUTE;
    }

    private function isAdminSiteaccess(SiteAccess $siteaccess) : bool
    {
        return in_array(
            $siteaccess->name,
            $this->siteaccessGroups[$this->getAdminSiteaccessGroupName()] ?? [],
            true
        );
    }

    private function getAdminSiteaccessGroupName(): string
    {
        if (class_exists(EzPlatformAdminUiBundle::class)) {
            return EzPlatformAdminUiBundle::ADMIN_GROUP_NAME;
        }

        return 'admin_group';
    }

    private function generateUrlAlias(Request $request): string
    {
        $parameters = [
            'contentId' => $request->attributes->getInt('contentId'),
        ];

        $locationId = $request->attributes->get('locationId');

        if ($locationId !== null) {
            $parameters['locationId'] = (int) $locationId;
        }

        return $this->router->generate(UrlAliasRouter::URL_ALIAS_ROUTE_NAME, $parameters);
    }
}
