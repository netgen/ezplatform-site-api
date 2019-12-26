<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for Site API content embed view rendering.
 */
class EzEmbeddedContentViewExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_ez_view_content_embedded',
                [EzEmbeddedContentViewRuntime::class, 'renderEmbeddedContentView'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
