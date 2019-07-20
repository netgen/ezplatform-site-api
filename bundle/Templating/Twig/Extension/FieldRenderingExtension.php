<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for content fields rendering (view).
 */
class FieldRenderingExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_render_field',
                [FieldRenderingRuntime::class, 'renderField'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
