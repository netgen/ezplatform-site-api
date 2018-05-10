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
