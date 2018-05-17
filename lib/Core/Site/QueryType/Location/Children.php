<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Netgen\EzPlatformSiteApi\API\Values\Location as SiteLocation;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Children Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
final class Children extends Location
{
    public static function getName()
    {
        return 'SiteAPI:Location/Children';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->remove(['depth', 'parent_location_id', 'subtree']);
        $resolver->setRequired('location');
        $resolver->setAllowedTypes('location', SiteLocation::class);

        $resolver->setDefault('sort', function (Options $options) {
            /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
            $location = $options['location'];
            return $location->innerLocation->getSortClauses();
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getFilterCriteria(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
        $location = $parameters['location'];

        return new ParentLocationId($location->id);
    }
}
