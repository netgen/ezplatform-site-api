<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for eZ Platform content view rendering.
 */
class EzContentViewExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_ez_view_content',
                [EzContentViewRuntime::class, 'renderContentView'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
