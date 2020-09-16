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
    abstract public function hasContent(string $name): bool;

    abstract public function getContent(string $name): Content;

    abstract public function hasLocation(string $name): bool;

    abstract public function getLocation(string $name): Location;

    abstract public function hasTag(string $name): bool;

    abstract public function getTag(string $name): Tag;
}
