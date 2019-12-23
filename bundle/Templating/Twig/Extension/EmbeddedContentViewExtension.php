<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for Site API content embed view rendering.
 */
class EmbeddedContentViewExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_view_content_embedded',
                [EmbeddedContentViewRuntime::class, 'renderEmbeddedContentView'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
