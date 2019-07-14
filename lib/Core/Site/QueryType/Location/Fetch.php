<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

/**
 * Fetch Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
class Fetch extends Location
{
    public static function getName(): string
    {
        return 'SiteAPI:Location/Fetch';
    }
}
