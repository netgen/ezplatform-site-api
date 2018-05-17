<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Base;

use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test stub for Base QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base
 */
class BaseQueryType extends Base
{
    protected function buildQuery()
    {
        return new Query();
    }

    public static function getName()
    {
        return 'Test:Base';
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        // do nothing
    }

    protected function getFilterCriteria(array $parameters)
    {
        return null;
    }

    protected function getQueryCriterion(array $parameters)
    {
        return null;
    }

    protected function getFacetBuilders(array $parameters)
    {
        return [];
    }

    protected function registerCriterionBuilders()
    {
        // do nothing
    }
}
