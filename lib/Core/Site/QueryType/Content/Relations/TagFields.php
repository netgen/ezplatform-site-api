<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use InvalidArgumentException;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * QueryType for finding specific Tag fields relations in a given Content.
 */
final class TagFields extends Content
{
    public static function getName(): string
    {
        return 'SiteAPI:Content/Relations/TagFields';
    }

    /**
     * @inheritdoc
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
        $resolver->setAllowedTypes('relation_field', ['string', 'array']);
        $resolver->setAllowedValues(
            'relation_field',
            static function ($fields) {
                if (!is_array($fields)) {
                    return true;
                }

                foreach ($fields as $field) {
                    if (!is_string($field)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $resolver->setDefined('exclude_self');
        $resolver->setAllowedTypes('exclude_self', ['bool']);
        $resolver->setDefaults([
            'exclude_self' => true,
        ]);
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
        /** @var string[] $fields */
        $fields = (array) $parameters['relation_field'];

        $tagIds = $this->extractTagIds($content, $fields);

        if (empty($tagIds)) {
            return new MatchNone();
        }

        $criteria = [];
        $criteria[] = new TagId($tagIds);

        if ($parameters['exclude_self']) {
            $criteria[] = new LogicalNot(new ContentId($content->id));
        }

        return $criteria;
    }

    /**
     * Extract Tag IDs from $fields in the given $content.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Values\Content $content
     * @param string[] $fields
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function extractTagIds(SiteContent $content, array $fields): array
    {
        $tagsIdsGrouped = [[]];

        foreach ($fields as $identifier) {
            if (!$content->hasField($identifier)) {
                throw new InvalidArgumentException(
                    "Content does not contain field '{$identifier}'"
                );
            }

            $field = $content->getField($identifier);

            if ($field->fieldTypeIdentifier !== 'eztags') {
                throw new InvalidArgumentException(
                    "Field '{$identifier}' is not of 'eztags' type"
                );
            }

            /** @var $value \Netgen\TagsBundle\Core\FieldType\Tags\Value */
            $value = $field->value;
            $tagsIdsGrouped[] = array_map(static function (Tag $tag) {return $tag->id;}, $value->tags);
        }

        return array_merge(...$tagsIdsGrouped);
    }
}
