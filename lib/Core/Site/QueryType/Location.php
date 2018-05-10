<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base implementation for Location search QueryTypes.
 */
abstract class Location extends Base
{
    final protected function configureBaseOptions(OptionsResolver $resolver)
    {
        parent::configureBaseOptions($resolver);

        $resolver->setDefined([
            'depth',
            'main',
            'parent_location_id',
            'priority',
            'subtree',
            'visible',
        ]);

        $resolver->setAllowedTypes('parent_location_id', ['int', 'int[]', 'string', 'string[]']);
        $resolver->setAllowedTypes('subtree', ['string', 'string[]']);
        $resolver->setAllowedTypes('depth', ['int', 'int[]', 'array']);
        $resolver->setAllowedTypes('priority', ['int', 'int[]', 'array']);

        $resolver->setAllowedValues('main', [true, false, null]);
        $resolver->setAllowedValues('visible', [true, false, null]);
    }

    protected function buildQuery()
    {
        return new LocationQuery();
    }
}
