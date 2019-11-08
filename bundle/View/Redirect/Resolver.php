<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect;

use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
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
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\Redirect\RedirectConfiguration $redirectConfig
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @return string
     */
    public function resolveTarget(RedirectConfiguration $redirectConfig, ContentView $view): string
    {
        if (\strpos($redirectConfig->getTarget(), '@=') === 0) {
            return $this->processExpression($redirectConfig, $view);
        }

        if (\strpos($redirectConfig->getTarget(), 'http') === 0) {
            return $this->processUrl($redirectConfig);
        }

        return $this->router->generate(
            $redirectConfig->getTarget(),
            $redirectConfig->getTargetParameters()
        );
    }

    private function processExpression(RedirectConfiguration $redirectConfig, ContentView $view): string
    {
        $value = $this->parameterProcessor->process(
            $redirectConfig->getTarget(),
            $view
        );

        if ($value instanceof Location || $value instanceof Content || $value instanceof Tag) {
            return $this->router->generate(
                $value,
                $redirectConfig->getTargetParameters()
            );
        }

        return '/';
    }

    private function processUrl(RedirectConfiguration $redirectConfig): string
    {
        $url = $redirectConfig->getTarget();

        $queryStringArray = [];
        foreach ($redirectConfig->getTargetParameters() as $key => $value) {
            $queryStringArray[] = urlencode($key).'='.urlencode($value);
        }

        return count($queryStringArray) > 0 ? $url.'?'.implode('&', $queryStringArray) : $url;
    }
}
