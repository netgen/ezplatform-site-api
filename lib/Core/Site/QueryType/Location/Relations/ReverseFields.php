<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location\Relations;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\FieldRelation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Location;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * QueryType for finding reverse relations from specific fields towards a Content.
 *
 * Note: only visible main Locations of the related Content will be used.
 */
final class ReverseFields extends Location
{
    public static function getName(): string
    {
        return 'SiteAPI:Location/Relations/ReverseFields';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'content',
            'relation_field',
        ]);

        $resolver->setAllowedTypes('content', SiteContent::class);
        $resolver->setAllowedTypes('relation_field', ['string', 'string[]']);

        $resolver->setDefaults([
            'main' => true,
            'visible' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function getFilterCriteria(array $parameters)
    {
        $fields = (array) $parameters['relation_field'];

        if (empty($fields)) {
            return new MatchNone();
        }

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        $criteria = [];

        foreach ($fields as $identifier) {
            $criteria[] = new FieldRelation($identifier, Operator::CONTAINS, [$content->id]);
        }

        return $criteria;
    }
}
