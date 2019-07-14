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
            'visible',
        ]);

        $resolver->setAllowedTypes('parent_location_id', ['int', 'string', 'array']);
        $resolver->setAllowedValues(
            'parent_location_id',
            static function ($ids): bool {
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
            static function ($subtrees): bool {
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

    protected function buildQuery(): Query
    {
        return new LocationQuery();
    }
}
