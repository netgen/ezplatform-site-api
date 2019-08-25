<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Base;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test stub for Base QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base
 */
class BaseQueryType extends Base
{
    public static function getName(): string
    {
        return 'Test:Base';
    }

    protected function buildQuery(): Query
    {
        return new Query();
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        // do nothing
    }

    protected function getFilterCriteria(array $parameters)
    {
        return null;
    }

    protected function getQueryCriterion(array $parameters): ?Criterion
    {
        return null;
    }

    protected function getFacetBuilders(array $parameters): array
    {
        return [];
    }

    protected function registerCriterionBuilders(): void
    {
        // do nothing
    }
}
