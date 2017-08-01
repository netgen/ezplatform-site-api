<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\EzPlatformSiteApi\API\RelationService as RelationServiceInterface;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content;

class RelationService implements RelationServiceInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function loadFieldRelations($contentId, $fieldDefinitionIdentifier, array $contentTypeIdentifiers = [])
    {
        $content = $this->site->getLoadService()->loadContent($contentId);

        $field = $content->getField($fieldDefinitionIdentifier);
        if ($field->fieldTypeIdentifier !== 'ezobjectrelationlist') {
            throw new NotImplementedException(
                'Loading field relations is supported only for RelationList field type'
            );
        }

        /** @var \eZ\Publish\Core\FieldType\RelationList\Value $value */
        $value = $field->value;
        $relatedContentIdList = $value->destinationContentIds;
        $criteria = [];

        $criteria[] = new ContentId($relatedContentIdList);
        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        if (count($criteria) > 1) {
            $criteria = new LogicalAnd($criteria);
        } else {
            $criteria = reset($criteria);
        }

        $query = new Query([
            'filter' => $criteria,
            'limit' => count($relatedContentIdList),
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);
        $relatedContentList = array_map(
            function(SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );

        $sortedIdList = array_flip($relatedContentIdList);
        $sorter = function (Content $content1, Content $content2) use ($sortedIdList) {
            if ($content1->id === $content2->id) {
                return 0;
            }

            return ($sortedIdList[$content1->id] < $sortedIdList[$content2->id]) ? -1 : 1;
        };
        usort($relatedContentList, $sorter);

        return $relatedContentList;
    }
}
