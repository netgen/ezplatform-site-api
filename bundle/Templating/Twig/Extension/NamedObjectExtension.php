<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for access to named objects.
 */
class NamedObjectExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ng_named_content',
                [NamedObjectRuntime::class, 'getNamedContent']
            ),
            new TwigFunction(
                'ng_named_location',
                [NamedObjectRuntime::class, 'getNamedLocation']
            ),
            new TwigFunction(
                'ng_named_tag',
                [NamedObjectRuntime::class, 'getNamedTag']
            ),
        ];
    }
}
