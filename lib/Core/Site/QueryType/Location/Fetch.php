<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Fetch Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
class Fetch extends Location
{
    public static function getName()
    {
        return 'SiteAPI:Location/Fetch';
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        // do nothing
    }

    protected function getFilterCriteria(array $parameters)
    {
        return null;
    }

    protected function getQueryCriteria(array $parameters)
    {
        return null;
    }

    protected function getFacetBuilders(array $parameters)
    {
        return [];
    }
}
