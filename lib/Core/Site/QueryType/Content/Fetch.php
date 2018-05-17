<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;

use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Fetch Content QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content
 */
class Fetch extends Content
{
    public static function getName()
    {
        return 'SiteAPI:Content/Fetch';
    }
}
