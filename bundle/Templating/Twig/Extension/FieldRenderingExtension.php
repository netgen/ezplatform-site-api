<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for content fields rendering (view).
 */
class FieldRenderingExtension extends AbstractExtension
{
    public function getFunctions()
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
