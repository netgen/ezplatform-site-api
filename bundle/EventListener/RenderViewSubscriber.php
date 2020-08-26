<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Netgen\Bundle\EzPlatformSiteApiBundle\Event\RenderViewEvent;
use Netgen\Bundle\EzPlatformSiteApiBundle\Events;
use Netgen\EzPlatformSiteApi\Event\SiteApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @uses \Netgen\Bundle\EzPlatformSiteApiBundle\Events::RENDER_VIEW
 */
final class RenderViewSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $responseTagger;

    public function __construct(ResponseTagger $responseTagger)
    {
        $this->responseTagger = $responseTagger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::RENDER_VIEW => 'tagResponse',
            SiteApiEvents::RENDER_CONTENT => 'tagResponse',
        ];
    }

    public function tagResponse(RenderViewEvent $event): void
    {
        $this->responseTagger->tag($event->getView());
    }
}
