<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle;

final class Events
{
    /**
     * Dispatched when the view is rendered through "ng_view_content" Twig function,
     * without using a sub-request.
     *
     * @see \Netgen\Bundle\EzPlatformSiteApiBundle\EventListener\ViewTaggerListener
     */
    public const NG_VIEW_CONTENT_RENDER_VIEW = 'netgen_site_api.ng_view_content_render_view';
}
