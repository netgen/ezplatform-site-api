<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle;

final class Events
{
    /**
     * Dispatched when the view is rendered through ViewRenderer, without using a sub-request.
     *
     * @see \Netgen\Bundle\EzPlatformSiteApiBundle\EventListener\ViewTaggerSubscriber
     * @see \Netgen\Bundle\EzPlatformSiteApiBundle\View\ViewRenderer
     */
    public const RENDER_VIEW = 'netgen_site_api.render_view';
}
