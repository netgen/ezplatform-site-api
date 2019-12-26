<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Bundle\EzPublishCoreBundle\View\Provider\Configured as CoreConfigured;
use eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface;
use eZ\Publish\Core\MVC\Symfony\View\View;

final class CoreOverride extends CoreConfigured
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider\ContentViewFallbackResolver
     */
    private $contentViewFallbackResolver;

    public function __construct(
        MatcherFactoryInterface $matcherFactory,
        ContentViewFallbackResolver $contentViewFallbackResolver
    ) {
        parent::__construct($matcherFactory);

        $this->contentViewFallbackResolver = $contentViewFallbackResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getView(View $view): ?View
    {
        // Service is dispatched by the configured view class, so this should be safe
        /** @var \eZ\Publish\Core\MVC\Symfony\View\ContentView $view */
        $configHash = $this->matcherFactory->match($view);

        if ($configHash === null) {
            return $this->contentViewFallbackResolver->getSiteApiFallbackDto($view);
        }

        return $this->buildContentView($configHash);
    }
}
