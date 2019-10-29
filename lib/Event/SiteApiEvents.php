<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Event;

/**
 * @deprecated since 3.1, to be removed in 4.0. Use Events instead.
 *
 * @see \Netgen\Bundle\EzPlatformSiteApiBundle\Events
 */
class SiteApiEvents
{
    /**
     * Dispatched when the content is rendered without usage of sub-requests.
     */
    public const RENDER_CONTENT = 'netgen_site_api.render_content';
}
