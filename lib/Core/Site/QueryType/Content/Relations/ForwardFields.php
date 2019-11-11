<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * QueryType for finding relations from specific relation fields of a Content.
 */
final class ForwardFields extends Content
{
    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry
     */
    private $relationResolverRegistry;

    public function __construct(RelationResolverRegistry $relationResolverRegistry)
    {
        $this->relationResolverRegistry = $relationResolverRegistry;
    }

    public static function getName(): string
    {
        return 'SiteAPI:Content/Relations/ForwardFields';
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
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     */
    protected function getFilterCriteria(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        $fields = (array) $parameters['relation_field'];
        $idsGrouped = [[]];

        foreach ($fields as $identifier) {
            $field = $content->getField($identifier);
            $relationResolver = $this->relationResolverRegistry->get($field->fieldTypeIdentifier);
            $idsGrouped[] = $relationResolver->getRelationIds($field);
        }

        $relatedContentIds = \array_merge(...$idsGrouped);

        if (empty($relatedContentIds)) {
            return new MatchNone();
        }

        return new ContentId($relatedContentIds);
    }
}
