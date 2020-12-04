<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Location\IsMainLocation;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Netgen\EzPlatformSearchExtra\API\Values\Content\Query\Criterion\Visible;
use Netgen\EzPlatformSiteApi\API\RelationService as RelationServiceInterface;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Registry as RelationResolverRegistry;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;
use function array_flip;
use function array_slice;
use function count;
use function usort;

/**
 * @final
 *
 * @internal
 *
 * Hint against API interface instead of this service:
 *
 * @see \Netgen\EzPlatformSiteApi\API\RelationService
 */
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

    public function __construct(
        SiteInterface $site,
        RelationResolverRegistry $relationResolverRegistry
    ) {
        $this->site = $site;
        $this->relationResolverRegistry = $relationResolverRegistry;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadFieldRelation(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ): ?Content {
        $relatedContentItems = $this->loadFieldRelations(
            $content,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        return $relatedContentItems[0] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadFieldRelations(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        ?int $limit = null
    ): array {
        $field = $content->getField($fieldDefinitionIdentifier);
        $relationResolver = $this->relationResolverRegistry->get($field->fieldTypeIdentifier);

        $relatedContentIds = $relationResolver->getRelationIds($field);
        $relatedContentItems = $this->getRelatedContentItems(
            $relatedContentIds,
            $contentTypeIdentifiers,
            $limit
        );
        $this->sortByIdOrder($relatedContentItems, $relatedContentIds);

        return $relatedContentItems;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadLocationFieldRelation(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ): ?Location {
        $relatedLocations = $this->loadLocationFieldRelations(
            $content,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        return $relatedLocations[0] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadLocationFieldRelations(
        Content $content,
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        ?int $limit = null
    ): array {
        $field = $content->getField($fieldDefinitionIdentifier);
        $relationResolver = $this->relationResolverRegistry->get($field->fieldTypeIdentifier);

        $relatedContentIds = $relationResolver->getRelationIds($field);
        $relatedLocations = $this->getRelatedLocations(
            $relatedContentIds,
            $contentTypeIdentifiers,
            $limit
        );
        $this->sortLocationsByIdOrder($relatedLocations, $relatedContentIds);

        return $relatedLocations;
    }

    /**
     * Return an array of related Content items, optionally limited by $limit.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    private function getRelatedContentItems(array $relatedContentIds, array $contentTypeIdentifiers, ?int $limit = null): array
    {
        if (count($relatedContentIds) === 0) {
            return [];
        }

        $criteria = [
            new ContentId($relatedContentIds),
        ];

        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        if (!$this->site->getSettings()->showHiddenItems) {
            $criteria[] = new Visible(true);
        }

        $query = new Query([
            'filter' => new LogicalAnd($criteria),
            'limit' => count($relatedContentIds),
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);
        $contentItems = $this->extractContentItems($searchResult);

        if ($limit !== null) {
            return array_slice($contentItems, 0, $limit);
        }

        return $contentItems;
    }

    /**
     * Return an array of related Content items, optionally limited by $limit.
     *
     * @param array $relatedContentIds
     * @param array $contentTypeIdentifiers
     * @param null|int $limit
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content[]
     */
    private function getRelatedLocations(array $relatedContentIds, array $contentTypeIdentifiers, ?int $limit = null): array
    {
        if (\count($relatedContentIds) === 0) {
            return [];
        }

        $criteria = [
            new ContentId($relatedContentIds),
            new IsMainLocation(IsMainLocation::MAIN),
            new Visible(true),
        ];

        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        $query = new LocationQuery([
            'filter' => new LogicalAnd($criteria),
            'limit' => \count($relatedContentIds),
        ]);

        $searchResult = $this->site->getFilterService()->filterLocations($query);
        $locations = $this->extractLocations($searchResult);

        if ($limit !== null) {
            return \array_slice($locations, 0, $limit);
        }

        return $locations;
    }

    /**
     * Sorts $relatedContentItems to match order from $relatedContentIds.
     */
    private function sortByIdOrder(array &$relatedContentItems, array $relatedContentIds): void
    {
        $sortedIdList = array_flip($relatedContentIds);

        $sorter = static function (Content $content1, Content $content2) use ($sortedIdList): int {
            return $sortedIdList[$content1->id] <=> $sortedIdList[$content2->id];
        };

        usort($relatedContentItems, $sorter);
    }

    /**
     * Sorts $relatedLocations to match order from $relatedContentIds.
     *
     * @param array $relatedLocations
     * @param array $relatedContentIds
     */
    private function sortLocationsByIdOrder(array &$relatedLocations, array $relatedContentIds): void
    {
        $sortedIdList = \array_flip($relatedContentIds);

        $sorter = static function (Location $location1, Location $location2) use ($sortedIdList): int {
            return $sortedIdList[$location1->contentId] <=> $sortedIdList[$location2->contentId];
        };

        \usort($relatedLocations, $sorter);
    }
}
