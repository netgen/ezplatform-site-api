<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Netgen\Bundle\EzPlatformSiteApiBundle\Event\RenderViewEvent;
use Netgen\Bundle\EzPlatformSiteApiBundle\Events;
use Netgen\EzPlatformSiteApi\Event\RenderContentEvent;
use Netgen\EzPlatformSiteApi\Event\SiteApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @uses \Netgen\Bundle\EzPlatformSiteApiBundle\Events::NG_VIEW_CONTENT_RENDER_VIEW
 */
final class ViewTaggerListener implements EventSubscriberInterface
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
            SiteApiEvents::RENDER_CONTENT => 'onRenderContent',
            Events::NG_VIEW_CONTENT_RENDER_VIEW => 'onNgViewContentRenderView',
        ];
    }

    public function onRenderContent(RenderContentEvent $event): void
    {
        $this->responseTagger->tag($event->getView());
    }

    public function onNgViewContentRenderView(RenderViewEvent $event): void
    {
        $this->responseTagger->tag($event->getView());
    }
}
