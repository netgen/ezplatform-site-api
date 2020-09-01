<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Netgen\EzPlatformSiteApi\API\Values\Location as SiteLocation;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Siblings Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
final class Siblings extends Location
{
    public static function getName(): string
    {
        return 'SiteAPI:Location/Siblings';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->remove(['depth', 'parent_location_id', 'subtree']);
        $resolver->setRequired('location');
        $resolver->setAllowedTypes('location', SiteLocation::class);

        $resolver->setDefault(
            'sort',
            static function (Options $options): array {
                /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
                $location = $options['location'];

                try {
                    return $location->parent->innerLocation->getSortClauses();
                } catch (NotImplementedException $e) {
                    return [];
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion[]
     */
    protected function getFilterCriteria(array $parameters): array
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
        $location = $parameters['location'];

        return [
            new ParentLocationId($location->parentLocationId),
            new LogicalNot(new LocationId($location->id)),
        ];
    }
}
