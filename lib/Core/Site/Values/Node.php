<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use Netgen\EzPlatformSiteApi\API\Values\Node as APINode;

final class Node extends APINode
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected $content;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    protected $contentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $innerLocation;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[][]
     */
    private $childrenCache = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[][]
     */
    private $siblingCache = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalParent;

    public function __construct(array $properties = [])
    {
        if (array_key_exists('site', $properties)) {
            $this->site = $properties['site'];
            unset($properties['site']);
        }

        parent::__construct($properties);
    }

    /**
     * Magic getter for retrieving convenience properties.
     *
     * @param string $property The name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'contentId':
                return $this->contentInfo->id;
            case 'children':
                return $this->getChildren();
            case 'siblings':
                return $this->getSiblings();
            case 'parent':
                return $this->getParent();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->innerLocation, $property)) {
            return $this->innerLocation->$property;
        }

        return parent::__get($property);
    }

    /**
     * Magic isset for signaling existence of convenience properties.
     *
     * @param string $property
     *
     * @return bool
     */
    public function __isset($property)
    {
        switch ($property) {
            case 'contentId':
            case 'children':
            case 'siblings':
            case 'parent':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerLocation, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function getChildren($limit = 25, array $contentTypeIdentifiers = [])
    {
        $cacheId = $this->getCacheId($contentTypeIdentifiers, $limit);

        if (!array_key_exists($cacheId, $this->childrenCache)) {
            $criteria = [
                new ParentLocationId($this->id),
                new Visibility(Visibility::VISIBLE),
            ];

            if (!empty($contentTypeIdentifiers)) {
                $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
            }

            $searchResult = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        'filter' => new LogicalAnd($criteria),
                        'sortClauses' => $this->innerLocation->getSortClauses(),
                        'limit' => $limit,
                    ]
                )
            );
            $this->childrenCache[$cacheId] = $this->extractValuesFromSearchResult($searchResult);
        }

        return $this->childrenCache[$cacheId];
    }

    public function getSiblings($limit = 25, array $contentTypeIdentifiers = [])
    {
        $cacheId = $this->getCacheId($contentTypeIdentifiers, $limit);

        if (!array_key_exists($cacheId, $this->siblingCache)) {
            $criteria = [
                new ParentLocationId($this->parentLocationId),
                new LogicalNot(
                    new LocationId($this->id)
                ),
                new Visibility(Visibility::VISIBLE),
            ];

            if (!empty($contentTypeIdentifiers)) {
                $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
            }

            $searchResult = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        'filter' => new LogicalAnd($criteria),
                        'sortClauses' => $this->innerLocation->getSortClauses(),
                        'limit' => $limit,
                    ]
                )
            );
            $this->siblingCache[$cacheId] = $this->extractValuesFromSearchResult($searchResult);
        }

        return $this->siblingCache[$cacheId];
    }

    /**
     * Extracts value objects from the given $searchResult.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Search\SearchResult $searchResult
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject[]
     */
    private function extractValuesFromSearchResult(SearchResult $searchResult)
    {
        $valueObjects = [];

        foreach ($searchResult->searchHits as $searchHit) {
            $valueObjects[] = $searchHit->valueObject;
        }

        return $valueObjects;
    }

    /**
     * Returns unique string for the given parameters.
     *
     * @param array $contentTypeIdentifiers
     * @param int $limit
     *
     * @return string
     */
    private function getCacheId(array $contentTypeIdentifiers, $limit)
    {
        sort($contentTypeIdentifiers);

        return md5(implode(' ', $contentTypeIdentifiers) . ' ' . $limit);
    }

    private function getParent()
    {
        if ($this->internalParent === null) {
            $this->internalParent = $this->site->getLoadService()->loadLocation(
                $this->parentLocationId
            );
        }

        return $this->internalParent;
    }
}
