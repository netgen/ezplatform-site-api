<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryTypes;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use eZ\Publish\Core\QueryType\OptionsResolverBasedQueryType;
use eZ\Publish\Core\QueryType\QueryType;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * QueryType for finding specific field relations of a Content.
 */
class ForwardFieldRelations extends OptionsResolverBasedQueryType implements QueryType
{
    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry
     */
    private $relationResolverRegistry;

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry $relationResolverRegistry
     */
    public function __construct(RelationResolverRegistry $relationResolverRegistry)
    {
        $this->relationResolverRegistry = $relationResolverRegistry;
    }

    public static function getName()
    {
        return 'SiteAPI:ForwardFieldRelations';
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
            'field_definition_identifier',
        ]);

        $optionsResolver->setDefaults([
            'limit' => 25,
            'offset' => 0,
            'content_type_identifiers' => [],
            'sort_clauses' => [],
        ]);

        $optionsResolver->setAllowedTypes('content', Content::class);
        $optionsResolver->setAllowedTypes('field_definition_identifier', 'string');
        $optionsResolver->setAllowedTypes('limit', 'int');
        $optionsResolver->setAllowedTypes('offset', 'int');
        $optionsResolver->setAllowedTypes('content_type_identifiers', 'string[]');
        $optionsResolver->setAllowedTypes('sort_clauses', 'eZ\Publish\API\Repository\Values\Content\Query\SortClause[]');
    }

    /**
     * todo test empty id search solr/db
     * @inheritdoc
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function doGetQuery(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        $fieldDefinitionIdentifier = $parameters['field_definition_identifier'];
        $contentTypeIdentifiers = $parameters['content_type_identifiers'];
        $limit = $parameters['limit'];
        $offset = $parameters['offset'];
        $sortClauses = $parameters['sort_clauses'];

        if (!$content->hasField($fieldDefinitionIdentifier)) {
            throw new RuntimeException(
                "Content does not contain a field '{$fieldDefinitionIdentifier}'"
            );
        }

        $field = $content->getField($fieldDefinitionIdentifier);
        $relationResolver = $this->relationResolverRegistry->get($field->fieldTypeIdentifier);
        $relatedContentIds = $relationResolver->getRelationIds($field);

        $query = new Query([
            'filter' => $this->buildCriteria($relatedContentIds, $contentTypeIdentifiers),
            'limit' => $limit,
            'offset' => $offset,
            'sortClauses' => $sortClauses,
        ]);

        return $query;
    }

    /**
     * Build Query criteria by the given parameters.
     *
     * @param int[]|string[] $contentIds
     * @param string[] $contentTypeIdentifiers
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId|\eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd|\eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone
     * @throws \InvalidArgumentException
     */
    private function buildCriteria(array $contentIds, array $contentTypeIdentifiers)
    {
        if (empty($contentIds)) {
            return new MatchNone();
        }

        $criteria = new ContentId($contentIds);

        if (!empty($contentTypeIdentifiers)) {
            $criteria = new LogicalAnd([
                $criteria,
                new ContentTypeIdentifier($contentTypeIdentifiers),
            ]);
        }

        return $criteria;
    }
}
