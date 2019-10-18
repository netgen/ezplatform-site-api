<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Event;

final class SiteApiEvents
{
    /**
     * Dispatched when the content is rendered without usage of sub-requests.
     */
    public const RENDER_CONTENT = 'netgen_site_api.render_content';
}
