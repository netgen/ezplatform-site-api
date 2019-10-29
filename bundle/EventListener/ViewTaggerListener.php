<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Netgen\Bundle\EzPlatformSiteApiBundle\Event\RenderViewEvent;
use Netgen\Bundle\EzPlatformSiteApiBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @uses \Netgen\Bundle\EzPlatformSiteApiBundle\Events::NG_VIEW_CONTENT_RENDER
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
        return [Events::NG_VIEW_CONTENT_RENDER => 'onRenderView'];
    }

    public function onRenderView(RenderViewEvent $event): void
    {
        $this->responseTagger->tag($event->getView());
    }
}
