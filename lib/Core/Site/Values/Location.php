<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;

final class Location extends APILocation
{
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
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $childrenCache = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalParent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $internalContent;

    public function __construct(array $properties = [])
    {
        if (array_key_exists('site', $properties)) {
            $this->site = $properties['site'];
            unset($properties['site']);
        }

        parent::__construct($properties);
    }

    /**
     * @inheritdoc
     *
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
            case 'parent':
                return $this->getParent();
            case 'content':
                return $this->getContent();
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
            case 'parent':
            case 'content':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerLocation, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function getChildren(array $contentTypeIdentifiers = [], $limit = 10)
    {
        $cacheId = $this->getChildrenCacheId($contentTypeIdentifiers, $limit);
        $criteria = [];
        $criteria[] = new ParentLocationId($this->innerLocation->id);

        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        if (count($criteria) > 1) {
            $criteria = new LogicalAnd($criteria);
        }

        if (!array_key_exists($cacheId, $this->childrenCache)) {
            $this->childrenCache[$cacheId] = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        'filter' => $criteria,
                        'sortClauses' => $this->innerLocation->getSortClauses(),
                        'limit' => $limit,
                    ]
                )
            );
        }

        return $this->childrenCache[$cacheId];
    }

    /**
     * Returns unique string for the given parameters.
     *
     * @param array $contentTypeIdentifiers
     * @param int $limit
     *
     * @return string
     */
    private function getChildrenCacheId(array $contentTypeIdentifiers, $limit)
    {
        sort($contentTypeIdentifiers);

        return md5(implode(' ', $contentTypeIdentifiers) . ' ' . $limit);
    }

    private function getParent()
    {
        if ($this->internalParent === null) {
            $this->internalParent = $this->site->getLoadService()->loadLocation(
                $this->innerLocation->parentLocationId
            );
        }

        return $this->internalParent;
    }

    private function getContent()
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->site->getLoadService()->loadContent(
                $this->contentInfo->id
            );
        }

        return $this->internalContent;
    }
}
