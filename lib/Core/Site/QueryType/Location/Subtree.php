<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\Depth;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Subtree as SubtreeCriterion;
use Netgen\EzPlatformSiteApi\API\Values\Location as SiteLocation;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Subtree Location QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location
 */
final class Subtree extends Location
{
    public static function getName(): string
    {
        return 'SiteAPI:Location/Subtree';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->remove(['parent_location_id', 'subtree']);
        $resolver->setRequired(['location']);
        $resolver->setDefined([
            'exclude_self',
            'relative_depth',
        ]);

        $resolver->setAllowedTypes('location', [SiteLocation::class]);
        $resolver->setAllowedTypes('exclude_self', ['null', 'bool']);
        $resolver->setAllowedTypes('relative_depth', ['null', 'int', 'array']);

        $resolver->setDefaults([
            'exclude_self' => true,
        ]);
    }

    protected function registerCriterionBuilders(): void
    {
        $this->registerCriterionBuilder(
            'relative_depth',
            function (CriterionDefinition $definition, array $parameters): ?Depth {
                if ($definition->value === null) {
                    return null;
                }

                /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
                $location = $parameters['location'];
                $relativeDepth = $this->getRelativeDepthValue(
                    $location->depth,
                    $definition->value
                );

                return new Depth($definition->operator, $relativeDepth);
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

        $criteria = [];
        $criteria[] = new SubtreeCriterion($location->pathString);

        if ($parameters['exclude_self']) {
            $criteria[] = new LogicalNot(new LocationId($location->id));
        }

        return $criteria;
    }

    /**
     * @param int $startDepth
     * @param int|int[] $value
     *
     * @return int|int[] array
     */
    private function getRelativeDepthValue(int $startDepth, $value)
    {
        if (\is_array($value)) {
            return \array_map(
                static function (int $value) use ($startDepth): int {
                    return $startDepth + $value;
                },
                $value
            );
        }

        return $startDepth + $value;
    }
}
