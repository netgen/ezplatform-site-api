<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

/**
 * Named object provider abstract.
 */
abstract class Provider
{
    /**
     * @param string $name
     *
     * @return bool
     */
    abstract public function hasContent(string $name): bool;

    /**
     * @param string $name
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    abstract public function getContent(string $name): Content;

    /**
     * @param string $name
     *
     * @return bool
     */
    abstract public function hasLocation(string $name): bool;

    /**
     * @param string $name
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    abstract public function getLocation(string $name): Location;

    /**
     * @param string $name
     *
     * @return bool
     */
    abstract public function hasTag(string $name): bool;

    /**
     * @param string $name
     *
     * @return \Netgen\TagsBundle\API\Repository\Values\Tags\Tag
     */
    abstract public function getTag(string $name): Tag;
}
