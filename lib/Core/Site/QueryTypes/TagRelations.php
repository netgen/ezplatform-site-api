<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryTypes;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use eZ\Publish\Core\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * todo
 */
class TagRelations extends OptionsResolverBasedQueryType implements QueryType
{
    public static function getName()
    {
        return 'SiteAPI:TagRelations';
    }

    /**
     * @inheritdoc
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired([
            'content',
        ]);

        $optionsResolver->setDefaults([
            'limit' => 25,
            'offset' => 0,
            'content_type_identifiers' => [],
            'sort_clauses' => [],
        ]);

        $optionsResolver->setAllowedTypes('content', Content::class);
        $optionsResolver->setAllowedTypes('limit', 'int');
        $optionsResolver->setAllowedTypes('offset', 'int');
        $optionsResolver->setAllowedTypes('content_type_identifiers', 'string[]');
        $optionsResolver->setAllowedTypes(
            'sort_clauses',
            'eZ\Publish\API\Repository\Values\Content\Query\SortClause[]'
        );
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function doGetQuery(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        $contentTypeIdentifiers = $parameters['content_type_identifiers'];
        $limit = $parameters['limit'];
        $offset = $parameters['offset'];
        $sortClauses = $parameters['sort_clauses'];

        $tagsIdSets = [];

        foreach ($content->fields as $field) {
            if (!$field->fieldTypeIdentifier !== 'eztags') {
                continue;
            }

            /** @var \Netgen\TagsBundle\Core\FieldType\Tags\Value $value */
            $value = $field->value;
            $tagsIdSets[] = array_map(function (Tag $tag) {return $tag->id;}, $value->tags);
        }

        $tagIds = array_merge(...$tagsIdSets);
        $criteria = new TagId($tagIds);

        if (!empty($contentTypeIdentifiers)) {
            $criteria = new LogicalAnd([
                $criteria,
                new ContentTypeIdentifier($contentTypeIdentifiers),
            ]);
        }

        $query = new Query([
            'filter' => $criteria,
            'limit' => $limit,
            'offset' => $offset,
            'sortClauses' => $sortClauses,
        ]);

        return $query;
    }
}
