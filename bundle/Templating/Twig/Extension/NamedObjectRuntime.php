<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

/**
 * NamedObjectExtension runtime.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension\NamedObjectExtension
 */
class NamedObjectRuntime
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider
     */
    private $namedObjectProvider;

    public function __construct(Provider $specialObjectProvider)
    {
        $this->namedObjectProvider = $specialObjectProvider;
    }

    public function getNamedContent(string $name): ?Content
    {
        if ($this->namedObjectProvider->hasContent($name)) {
            return $this->namedObjectProvider->getContent($name);
        }

        return null;
    }

        public function getNamedLocation(string $name): ?Location
    {
        try {
            if ($this->namedObjectProvider->hasLocation($name)) {
                return $this->namedObjectProvider->getLocation($name);
            }

            return null;
        } catch (UnauthorizedException $e) {
            return null;
        }
    }

    public function getNamedTag(string $name): ?Tag
    {
        if ($this->namedObjectProvider->hasTag($name)) {
            return $this->namedObjectProvider->getTag($name);
        }

        return null;
    }
}
