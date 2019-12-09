<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;

final class ContentViewFallbackResolver
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var string
     */
    private $toEzPlatformFallbackTemplate;

    /**
     * @var string
     */
    private $toSiteApiFallbackTemplate;

    public function __construct(
        ConfigResolverInterface $configResolver,
        string $toEzPlatformFallbackTemplate,
        string $toSiteApiFallbackTemplate
    ) {
        $this->configResolver = $configResolver;
        $this->toEzPlatformFallbackTemplate = $toEzPlatformFallbackTemplate;
        $this->toSiteApiFallbackTemplate = $toSiteApiFallbackTemplate;
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    public function getEzPlatformFallbackDto(): ?ContentView
    {
        if ($this->isEzPlatformFallbackEnabled()) {
            return new ContentView($this->toEzPlatformFallbackTemplate, [], '_ng_fallback');
        }

        return null;
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    public function getSiteApiFallbackDto(): ?ContentView
    {
        if ($this->isSiteApiFallbackEnabled()) {
            return new ContentView($this->toSiteApiFallbackTemplate, [], '_ng_fallback');
        }

        return null;
    }

    private function isEzPlatformFallbackEnabled(): bool
    {
        return $this->isSiteApiContentViewEnabled() && $this->useContentViewFallback();
    }

    private function isSiteApiFallbackEnabled(): bool
    {
        return !$this->isSiteApiContentViewEnabled() && $this->useContentViewFallback();
    }

    private function isSiteApiContentViewEnabled(): bool
    {
        return $this->configResolver->getParameter('override_url_alias_view_action', 'netgen_ez_platform_site_api');
    }

    private function useContentViewFallback(): bool
    {
        return $this->configResolver->getParameter('ng_fallback_to_secondary_content_view');
    }
}
