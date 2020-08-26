<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\Renderer;
use eZ\Publish\Core\MVC\Symfony\View\View;
use LogicException;
use Netgen\Bundle\EzPlatformSiteApiBundle\Event\RenderViewEvent;
use Netgen\Bundle\EzPlatformSiteApiBundle\Events;
use Netgen\EzPlatformSiteApi\Event\RenderContentEvent;
use Netgen\EzPlatformSiteApi\Event\SiteApiEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @internal
 *
 * Renders View object using any controller without executing a subrequest
 *
 * @see \eZ\Publish\Core\MVC\Symfony\View\View
 */
final class ViewRenderer
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\HttpKernel\Controller\ControllerResolverInterface
     */
    private $controllerResolver;

    /**
     * @var \Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface
     */
    private $argumentResolver;

    /**
     * @var \eZ\Publish\Core\MVC\Symfony\View\Renderer
     */
    private $coreViewRenderer;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        RequestStack $requestStack,
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver,
        Renderer $coreViewRenderer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->requestStack = $requestStack;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->coreViewRenderer = $coreViewRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function render(View $view, array $parameters, bool $layout): string
    {
        $renderedContent = $this->doRender($view, $parameters, $layout);

        $this->eventDispatcher->dispatch(SiteApiEvents::RENDER_CONTENT, new RenderContentEvent($view));
        $this->eventDispatcher->dispatch(Events::RENDER_VIEW, new RenderViewEvent($view));

        return $renderedContent;
    }

    private function doRender(View $view, array $parameters, bool $layout): string
    {
        $controllerReference = $view->getControllerReference();

        if ($controllerReference === null) {
            return $this->coreViewRenderer->render($view);
        }

        $parameters['layout'] = $layout;

        return $this->renderController($view, $controllerReference, $parameters);
    }

    private function renderController(View $view, ControllerReference $controllerReference, array $arguments): string
    {
        $controller = $this->resolveController($controllerReference);
        $arguments = $this->resolveControllerArguments($view, $controller, $arguments);

        $result = \call_user_func_array($controller, $arguments);

        if ($result instanceof View) {
            return $this->coreViewRenderer->render($result);
        }

        if ($result instanceof Response) {
            return (string) $result->getContent();
        }

        throw new LogicException('Controller result must be ContentView or Response instance');
    }

    private function resolveController(ControllerReference $controllerReference): callable
    {
        $controllerRequest = new Request();
        $controllerRequest->attributes->set('_controller', $controllerReference->controller);
        $controller = $this->controllerResolver->getController($controllerRequest);

        if ($controller === false) {
            throw new NotFoundHttpException(
                \sprintf('Unable to find the controller "%s".', $controllerReference->controller)
            );
        }

        return $controller;
    }

    private function resolveControllerArguments(View $view, callable $controller, array $arguments): array
    {
        $request = $this->requestStack->getMasterRequest();

        if ($request === null) {
            throw new LogicException('A Request must be available.');
        }

        $request = $request->duplicate();
        $request->attributes->set('view', $view);
        $request->attributes->add($view->getParameters());
        $request->attributes->add($arguments);

        return $this->argumentResolver->getArguments($request, $controller);
    }
}
