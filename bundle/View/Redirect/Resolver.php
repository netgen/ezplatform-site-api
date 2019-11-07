<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect;

use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Symfony\Component\Routing\RouterInterface;

final class Resolver
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\QueryType\ParameterProcessor
     */
    private $parameterProcessor;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * Resolver constructor.
     *
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\ParameterProcessor $parameterProcessor
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(
        ParameterProcessor $parameterProcessor,
        RouterInterface $router
    ) {
        $this->parameterProcessor = $parameterProcessor;
        $this->router = $router;
    }

    /**
     * Builds a path to the redirect target.
     *
     * Value from configuration can be:
     *      - SiteAPI location
     *      - SiteAPI content
     *      - location id fetched from config resolver
     *
     * @param string $redirectConfig
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return string
     */
    public function resolveTarget(string $redirectConfig, ContentView $view): string
    {
        $value = $this->parameterProcessor->process($redirectConfig, $view);

        if (is_array($value)) {
            if (count($value) < 1) {
                return '/';
            }

            $value = reset($value);
        }

        if ($value instanceof Location || $value instanceof Content) {
            return $this->router->generate($value);
        }

        if (is_int($value) || is_string($value)) {
            return $this->router->generate(
                'ez_urlalias',
                [
                    'locationId' => $value
                ]
            );
        }

        return '/';
    }
}
