<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base implementation for Location search QueryTypes.
 */
abstract class Location extends Base
{
    final protected function configureBaseOptions(OptionsResolver $resolver): void
    {
        parent::configureBaseOptions($resolver);

        $resolver->setDefined([
            'depth',
            'main',
            'parent_location_id',
            'priority',
            'subtree',
        ]);

        $resolver->setAllowedTypes('parent_location_id', ['int', 'string', 'array']);
        $resolver->setAllowedTypes('subtree', ['string', 'array']);
        $resolver->setAllowedTypes('depth', ['int', 'array']);
        $resolver->setAllowedTypes('priority', ['int', 'array']);

        $resolver->setAllowedValues('main', [true, false, null]);
    }

    protected function buildQuery(): Query
    {
        return new LocationQuery();
    }
}
