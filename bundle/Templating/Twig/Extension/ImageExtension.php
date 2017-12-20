<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ng_image_alias',
                [ImageRuntime::class, 'getImageVariation'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
