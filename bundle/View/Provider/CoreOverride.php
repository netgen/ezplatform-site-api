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
        if (($configHash = $this->matcherFactory->match($view)) === null) {
            return $this->contentViewFallbackResolver->getSiteApiFallbackDto();
        }

        return $this->buildContentView($configHash);
    }
}
