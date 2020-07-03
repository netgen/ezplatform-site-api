<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Psr\Log\LoggerInterface;

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

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $isDebug;

    public function __construct(
        Provider $specialObjectProvider,
        LoggerInterface $logger,
        bool $isDebug
    ) {
        $this->namedObjectProvider = $specialObjectProvider;
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }

    public function getNamedContent(string $name): ?Content
    {
        try {
            if ($this->namedObjectProvider->hasContent($name)) {
                return $this->namedObjectProvider->getContent($name);
            }
        } catch (UnauthorizedException $e) {
            if ($this->isDebug) {
                $this->logger->critical($e->getMessage());
            }
        }

        return null;
    }

    public function getNamedLocation(string $name): ?Location
    {
        try {
            if ($this->namedObjectProvider->hasLocation($name)) {
                return $this->namedObjectProvider->getLocation($name);
            }
        } catch (UnauthorizedException $e) {
            if ($this->isDebug) {
                $this->logger->critical($e->getMessage());
            }
        }

        return null;
    }

    public function getNamedTag(string $name): ?Tag
    {
        try {
            if ($this->namedObjectProvider->hasTag($name)) {
                return $this->namedObjectProvider->getTag($name);
            }
        } catch (UnauthorizedException $e) {
            if ($this->isDebug) {
                $this->logger->critical($e->getMessage());
            }
        }

        return null;
    }
}
