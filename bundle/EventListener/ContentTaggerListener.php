<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\EventListener;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Netgen\EzPlatformSiteApi\Event\RenderContentEvent;
use Netgen\EzPlatformSiteApi\Event\SiteApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ContentTaggerListener implements EventSubscriberInterface
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
        return [SiteApiEvents::RENDER_CONTENT => 'onRenderContent'];
    }

    public function onRenderContent(RenderContentEvent $event): void
    {
        $this->responseTagger->tag($event->getView());
    }
}
