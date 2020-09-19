<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use eZ\Publish\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use RuntimeException;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use function is_object;

class LocationUrlAliasRouter implements ChainedRouterInterface, RequestMatcherInterface
{
    /**
     * @var \eZ\Publish\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator
     */
    protected $generator;

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    protected $requestContext;

    public function __construct(UrlAliasGenerator $generator, ?RequestContext $requestContext = null)
    {
        $this->generator = $generator;
        $this->requestContext = $requestContext ?? new RequestContext();
    }

    public function matchRequest(Request $request): array
    {
        throw new ResourceNotFoundException('ContentUrlAliasRouter does not support matching requests.');
    }

    /**
     * Generates a URL for Site API Location object, from the given parameters.
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if (!$this->supportsObject($parameters[RouteObjectInterface::ROUTE_OBJECT] ?? null)) {
            throw new RouteNotFoundException('Could not match route');
        }

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
        $location = $parameters[RouteObjectInterface::ROUTE_OBJECT];
        unset($parameters[RouteObjectInterface::ROUTE_OBJECT]);

        return $this->generator->generate(
            $location->innerLocation,
            $parameters,
            $referenceType
        );
    }

    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    public function setContext(RequestContext $context): void
    {
        $this->requestContext = $context;
        $this->generator->setRequestContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->requestContext;
    }

    public function match(string $pathinfo): array
    {
        throw new RuntimeException("The ContentUrlAliasRouter doesn't support the match() method.");
    }

    public function supports($name): bool
    {
        if (is_object($name)) {
            return $this->supportsObject($name);
        }

        return $name === '' || $name === 'cmf_routing_object';
    }

    public function supportsObject(?object $object): bool
    {
        return $object instanceof Location;
    }

    public function getRouteDebugMessage($name, array $parameters = []): string
    {
        if ($name instanceof RouteObjectInterface) {
            return 'Route with key ' . $name->getRouteKey();
        }

        if ($name instanceof SymfonyRoute) {
            return 'Route with pattern ' . $name->getPath();
        }

        return $name;
    }
}
