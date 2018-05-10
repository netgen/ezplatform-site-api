<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree as SubtreeCriterion;
use Netgen\EzPlatformSiteApi\API\Values\Location as SiteLocation;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Subtree Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
final class Subtree extends Location
{
    public static function getName()
    {
        return 'SiteAPI:Location/Subtree';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->remove(['parent_location_id', 'subtree']);
        $resolver->setRequired(['location']);
        $resolver->setDefined(['include_root']);

        $resolver->setAllowedTypes('location', SiteLocation::class);
        $resolver->setAllowedTypes('include_root', 'bool');

        $resolver->setDefaults([
            'include_root' => false,
        ]);
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

        $criteria = [];
        $criteria[] = new SubtreeCriterion($location->pathString);

        if (!$parameters['include_root']) {
            $criteria[] = new LogicalNot(new LocationId($location->id));
        }

        return $criteria;
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
