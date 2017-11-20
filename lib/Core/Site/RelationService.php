<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use Netgen\EzPlatformSiteApi\API\RelationService as RelationServiceInterface;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;

class RelationService implements RelationServiceInterface
{
    use SearchResultExtractorTrait;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry
     */
    private $relationResolverRegistry;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     * @param \Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry $relationResolverRegistry
     */
    public function __construct(
        SiteInterface $site,
        RelationResolverRegistry $relationResolverRegistry
    ) {
        $this->site = $site;
        $this->relationResolverRegistry = $relationResolverRegistry;
    }

    public function loadFieldRelation(
        $contentId,
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ) {
        $relatedContentItems = $this->loadFieldRelations(
            $contentId,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        return count($relatedContentItems) ? reset($relatedContentItems) : null;
    }

    public function loadFieldRelations(
        $contentId,
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ) {
        $content = $this->site->getLoadService()->loadContent($contentId);

        if (!$content->hasField($fieldDefinitionIdentifier)) {
            throw new InvalidArgumentException(
                '$fieldDefinitionIdentifier',
                "Content does not contain a field '{$fieldDefinitionIdentifier}'"
            );
        }

        $field = $content->getField($fieldDefinitionIdentifier);
        $relationResolver = $this->relationResolverRegistry->get($field->fieldTypeIdentifier);

        $relatedContentIds = $relationResolver->getRelationIds($field);
        $relatedContentItems = $this->getRelatedContentItems(
            $relatedContentIds,
            $contentTypeIdentifiers
        );
        $this->sortByIdOrder($relatedContentItems, $relatedContentIds);

        return $relatedContentItems;
    }

    /**
     * Return an array of related Content from the given arguments.
     *
     * @throws \InvalidArgumentException As thrown by the Search API
     *
     * @param array $relatedContentIds
     * @param array $contentTypeIdentifiers
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content[]
     */
    private function getRelatedContentItems(array $relatedContentIds, array $contentTypeIdentifiers)
    {
        if (count($relatedContentIds) === 0) {
            return [];
        }

        $criteria = new ContentId($relatedContentIds);

        if (!empty($contentTypeIdentifiers)) {
            $criteria = new LogicalAnd([
                $criteria,
                new ContentTypeIdentifier($contentTypeIdentifiers),
            ]);
        }

        $query = new Query([
            'filter' => $criteria,
            'limit' => count($relatedContentIds),
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);
        /** @var \eZ\Publish\API\Repository\Values\Content\Content[] $contentItems */
        $contentItems = $this->extractValueObjects($searchResult);

        return $contentItems;
    }

    /**
     * Sorts $relatedContentItems to match order from $relatedContentIds.
     *
     * @param array $relatedContentItems
     * @param array $relatedContentIds
     *
     * @return void
     */
    private function sortByIdOrder(array &$relatedContentItems, array $relatedContentIds)
    {
        $sortedIdList = array_flip($relatedContentIds);

        $sorter = function (Content $content1, Content $content2) use ($sortedIdList) {
            if ($content1->id === $content2->id) {
                return 0;
            }

            return ($sortedIdList[$content1->id] < $sortedIdList[$content2->id]) ? -1 : 1;
        };

        usort($relatedContentItems, $sorter);
    }
}
