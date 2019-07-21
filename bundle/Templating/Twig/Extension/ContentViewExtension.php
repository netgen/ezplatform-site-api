<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for content view rendering.
 */
class ContentViewExtension extends AbstractExtension
{
    /**
     * Note that this function is experimental. Please report any issues on https://github.com/netgen/ezplatform-site-api/issues
     *
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_view_content',
                [ContentViewRuntime::class, 'renderContentView'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
