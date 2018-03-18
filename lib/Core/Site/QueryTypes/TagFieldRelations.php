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
class TagFieldRelations extends OptionsResolverBasedQueryType implements QueryType
{
    public static function getName()
    {
        return 'SiteAPI:TagFieldsRelations';
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
            'field_definition_identifiers',
        ]);

        $optionsResolver->setDefaults([
            'use_pager' => true,
            'max_per_page' => 25,
            'current_page' => 1,
            'limit' => 25,
            'offset' => 0,
            'content_type_identifiers' => [],
            'sort_clauses' => [],
        ]);

        $optionsResolver->setAllowedTypes('content', Content::class);
        $optionsResolver->setAllowedTypes('field_definition_identifiers', 'string[]');
        $optionsResolver->setAllowedTypes('use_pager', 'bool');
        $optionsResolver->setAllowedTypes('max_per_page', 'int');
        $optionsResolver->setAllowedTypes('current_page', 'int');
        $optionsResolver->setAllowedTypes('limit', 'int');
        $optionsResolver->setAllowedTypes('offset', 'int');
        $optionsResolver->setAllowedTypes('content_type_identifiers', 'string[]');
        $optionsResolver->setAllowedTypes('sort_clauses', 'eZ\Publish\API\Repository\Values\Content\Query\SortClause[]');
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    protected function doGetQuery(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        /** @var string[] $fieldDefinitionIdentifiers */
        $fieldDefinitionIdentifiers = $parameters['field_definition_identifiers'];
        $contentTypeIdentifiers = $parameters['content_type_identifiers'];
        $limit = $parameters['limit'];
        $offset = $parameters['offset'];
        $sortClauses = $parameters['sort_clauses'];

        $tagsIdSets = [];

        foreach ($fieldDefinitionIdentifiers as $identifier) {
            if (!$content->hasField($identifier)) {
                continue;
            }

            $field = $content->getField($identifier);

            if (!$field->fieldTypeIdentifier !== 'eztags') {
                continue;
            }

            /** @var $value \Netgen\TagsBundle\Core\FieldType\Tags\Value */
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
