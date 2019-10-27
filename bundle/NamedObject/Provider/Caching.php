<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;

use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

/**
 * Caching named object provider provides named objects using an aggregated
 * (Loading) provider, caching them in the internal in-memory cache for the
 * subsequent calls.
 *
 * @internal do not depend on this service, it can be changed without warning
 */
final class Caching extends Provider
{
    /**
     * @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider
     */
    private $provider;

    /**
     * @var array
     */
    private $cache = [];

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function hasContent(string $name): bool
    {
        return $this->provider->hasContent($name);
    }

    public function getContent(string $name): Content
    {
        if (!isset($this->cache['content'][$name])) {
            $this->cache['content'][$name] = $this->provider->getContent($name);
        }

        return $this->cache['content'][$name];
    }

    public function hasLocation(string $name): bool
    {
        return $this->provider->hasLocation($name);
    }

    public function getLocation(string $name): Location
    {
        if (!isset($this->cache['location'][$name])) {
            $this->cache['location'][$name] = $this->provider->getLocation($name);
        }

        return $this->cache['location'][$name];
    }

    public function hasTag(string $name): bool
    {
        return $this->provider->hasTag($name);
    }

    public function getTag(string $name): Tag
    {
        if (!isset($this->cache['tag'][$name])) {
            $this->cache['tag'][$name] = $this->provider->getTag($name);
        }

        return $this->cache['tag'][$name];
    }
}
