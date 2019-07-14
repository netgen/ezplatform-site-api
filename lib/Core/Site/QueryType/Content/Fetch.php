<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;

use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;

/**
 * Fetch Content QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content
 */
class Fetch extends Content
{
    public static function getName(): string
    {
        return 'SiteAPI:Content/Fetch';
    }
}
