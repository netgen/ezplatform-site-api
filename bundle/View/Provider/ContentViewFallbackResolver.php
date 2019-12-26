<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView as SiteContentView;

final class ContentViewFallbackResolver
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var string
     */
    private $toEzPlatformEmbedFallbackTemplate;

    /**
     * @var string
     */
    private $toEzPlatformViewFallbackTemplate;

    /**
     * @var string
     */
    private $toSiteApiEmbedFallbackTemplate;

    /**
     * @var string
     */
    private $toSiteApiViewFallbackTemplate;

    public function __construct(
        ConfigResolverInterface $configResolver,
        string $toEzPlatformEmbedFallbackTemplate,
        string $toEzPlatformViewFallbackTemplate,
        string $toSiteApiEmbedFallbackTemplate,
        string $toSiteApiViewFallbackTemplate
    ) {
        $this->configResolver = $configResolver;
        $this->toEzPlatformEmbedFallbackTemplate = $toEzPlatformEmbedFallbackTemplate;
        $this->toEzPlatformViewFallbackTemplate = $toEzPlatformViewFallbackTemplate;
        $this->toSiteApiEmbedFallbackTemplate = $toSiteApiEmbedFallbackTemplate;
        $this->toSiteApiViewFallbackTemplate = $toSiteApiViewFallbackTemplate;
    }

    /**
     * @param \Netgen\Bundle\EzPlatformSiteApiBundle\View\ContentView $view
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    public function getEzPlatformFallbackDto(SiteContentView $view): ?ContentView
    {
        if (!$this->isEzPlatformFallbackEnabled()) {
            return null;
        }

        if ($view->isEmbed()) {
            return new ContentView($this->toEzPlatformEmbedFallbackTemplate);
        }

        return new ContentView($this->toEzPlatformViewFallbackTemplate);
    }

    /**
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentView $view
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     *
     * @return \eZ\Publish\Core\MVC\Symfony\View\ContentView
     */
    public function getSiteApiFallbackDto(ContentView $view): ?ContentView
    {
        if (!$this->isSiteApiFallbackEnabled()) {
            return null;
        }

        if ($view->isEmbed()) {
            return new ContentView($this->toSiteApiEmbedFallbackTemplate);
        }

        return new ContentView($this->toSiteApiViewFallbackTemplate);
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
