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

        $resolver->setAllowedTypes('parent_location_id', ['int', 'string', 'array']);
        $resolver->setAllowedValues(
            'parent_location_id',
            function ($ids) {
                if (!is_array($ids)) {
                    return true;
                }

                foreach ($ids as $id) {
                    if (!is_int($id) && !is_string($id)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $resolver->setAllowedTypes('subtree', ['string', 'array']);
        $resolver->setAllowedValues(
            'subtree',
            function ($subtrees) {
                if (!is_array($subtrees)) {
                    return true;
                }

                foreach ($subtrees as $subtree) {
                    if (!is_string($subtree)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $resolver->setAllowedTypes('depth', ['int', 'array']);
        $resolver->setAllowedTypes('priority', ['int', 'array']);

        $resolver->setAllowedValues('main', [true, false, null]);
        $resolver->setAllowedValues('visible', [true, false, null]);
    }

    protected function buildQuery()
    {
        return new LocationQuery();
    }
}
