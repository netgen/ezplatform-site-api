<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use eZ\Publish\API\Repository\Values\Content\Location as RepositoryLocation;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter;
use Pagerfanta\Pagerfanta;

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
     * {@inheritdoc}
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
            case 'parent':
            case 'content':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerLocation, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function getChildren($limit = 25)
    {
        return $this->filterChildren([], $limit)->getIterator();
    }

    public function filterChildren(array $contentTypeIdentifiers = [], $maxPerPage = 25, $currentPage = 1)
    {
        $criteria = [
            new ParentLocationId($this->id),
            new Visibility(Visibility::VISIBLE),
        ];

        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        $pager = new Pagerfanta(
            new LocationSearchFilterAdapter(
                new LocationQuery([
                    'filter' => new LogicalAnd($criteria),
                    'sortClauses' => $this->getSortClauses(),
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function getSiblings($limit = 25)
    {
        return $this->filterSiblings([], $limit)->getIterator();
    }

    public function filterSiblings(array $contentTypeIdentifiers = [], $maxPerPage = 25, $currentPage = 1)
    {
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

        $pager = new Pagerfanta(
            new LocationSearchFilterAdapter(
                new LocationQuery([
                    'filter' => new LogicalAnd($criteria),
                    'sortClauses' => $this->getSortClauses(),
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
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

    private function getContent()
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->site->getLoadService()->loadContent(
                $this->contentInfo->id
            );
        }

        return $this->internalContent;
    }

    /**
     * Map for Location sort fields to their respective SortClauses.
     *
     * Those not here (class name/identifier and modified subnode) are
     * missing/deprecated and will most likely be removed in the future.
     */
    static private $sortFieldMap = [
        RepositoryLocation::SORT_FIELD_PATH => SortClause\Location\Path::class,
        RepositoryLocation::SORT_FIELD_PUBLISHED => SortClause\DatePublished::class,
        RepositoryLocation::SORT_FIELD_MODIFIED => SortClause\DateModified::class,
        RepositoryLocation::SORT_FIELD_SECTION => SortClause\SectionIdentifier::class,
        RepositoryLocation::SORT_FIELD_DEPTH => SortClause\Location\Depth::class,
        //RepositoryLocation::SORT_FIELD_CLASS_IDENTIFIER => false,
        //RepositoryLocation::SORT_FIELD_CLASS_NAME => false,
        RepositoryLocation::SORT_FIELD_PRIORITY => SortClause\Location\Priority::class,
        RepositoryLocation::SORT_FIELD_NAME => SortClause\ContentName::class,
        //RepositoryLocation::SORT_FIELD_MODIFIED_SUBNODE => false,
        RepositoryLocation::SORT_FIELD_NODE_ID => SortClause\Location\Id::class,
        RepositoryLocation::SORT_FIELD_CONTENTOBJECT_ID => SortClause\ContentId::class,
    ];

    /**
     * Map for Location sort order to their respective Query SORT constants.
     */
    static private $sortOrderMap = [
        RepositoryLocation::SORT_ORDER_DESC => Query::SORT_DESC,
        RepositoryLocation::SORT_ORDER_ASC => Query::SORT_ASC,
    ];

    /**
     * Get SortClause objects built from Locations's sort options.
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotImplementedException If sort field has a deprecated/unsupported value which does not have a Sort Clause.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses()
    {
        if (!isset(static::$sortFieldMap[$this->sortField])) {
            throw new NotImplementedException(
                "Sort clause not implemented for Location sort field with value {$this->sortField}"
            );
        }

        $sortClause = new static::$sortFieldMap[$this->sortField]();
        $sortClause->direction = static::$sortOrderMap[$this->sortOrder];

        return [$sortClause];
    }
}
