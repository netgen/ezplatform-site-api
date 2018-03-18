<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\ContentSearchFilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\LocationSearchFilterAdapter;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @internal hint against API Content instead of this class
 * @see \Netgen\EzPlatformSiteApi\API\Values\Content
 */
final class Content extends APIContent
{
    use SearchResultExtractorTrait;

    /**
     * @var string|int
     */
    protected $id;

    /**
     * @var string|int
     */
    protected $mainLocationId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    protected $contentInfo;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    protected $fields;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected $owner;

    /**
     * @var \eZ\Publish\API\Repository\Values\User\User
     */
    protected $innerOwnerUser;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $innerContent;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    protected $innerVersionInfo;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private $fieldsById = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private $domainObjectMapper;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\UserService
     */
    private $userService;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    private $queryTypeRegistry;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalMainLocation;

    /**
     * Denotes if $owner property is initialized.
     *
     * @var bool
     */
    private $isOwnerInitialized = false;

    /**
     * Denotes if $innerOwnerUser property is initialized.
     *
     * @var bool
     */
    private $isInnerOwnerUserInitialized = false;

    public function __construct(array $properties = [])
    {
        $this->site = $properties['site'];
        $this->domainObjectMapper = $properties['domainObjectMapper'];
        $this->contentService = $properties['repository']->getContentService();
        $this->userService = $properties['repository']->getUserService();
        $this->repository = $properties['repository'];
        $this->queryTypeRegistry = $properties['queryTypeRegistry'];

        unset(
            $properties['site'],
            $properties['domainObjectMapper'],
            $properties['repository'],
            $properties['queryTypeRegistry']
        );

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
            case 'fields':
                $this->initializeFields();

                return $this->fields;
            case 'mainLocation':
                return $this->getMainLocation();
            case 'innerContent':
                return $this->getInnerContent();
            case 'versionInfo':
                return $this->innerVersionInfo;
            case 'contentInfo':
                return $this->getContentInfo();
            case 'owner':
                return $this->getOwner();
            case 'innerOwnerUser':
                return $this->getInnerOwnerUser();
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
            case 'contentInfo':
            case 'fields':
            case 'mainLocation':
            case 'innerContent':
            case 'versionInfo':
            case 'owner':
            case 'innerOwnerUser':
                return true;
        }

        return parent::__isset($property);
    }

    public function __debugInfo()
    {
        $this->initializeFields();

        return [
            'id' => $this->id,
            'mainLocationId' => $this->mainLocationId,
            'name' => $this->name,
            'languageCode' => $this->languageCode,
            'contentInfo' => $this->getContentInfo(),
            'fields' => $this->fields,
            //'mainLocation' => $this->getMainLocation(),
            //'innerContent' => $this->getInnerContent(),
            //'innerVersionInfo' => $this->innerVersionInfo,
        ];
    }

    public function hasField($identifier)
    {
        $this->initializeFields();

        return isset($this->fields[$identifier]);
    }

    public function getField($identifier)
    {
        $this->initializeFields();

        if ($this->hasField($identifier)) {
            return $this->fields[$identifier];
        }

        return null;
    }

    public function hasFieldById($id)
    {
        $this->initializeFields();

        return isset($this->fieldsById[$id]);
    }

    public function getFieldById($id)
    {
        $this->initializeFields();

        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id];
        }

        return null;
    }

    public function getFieldValue($identifier)
    {
        $this->initializeFields();

        if ($this->hasField($identifier)) {
            return $this->fields[$identifier]->value;
        }

        return null;
    }

    public function getFieldValueById($id)
    {
        $this->initializeFields();

        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id]->value;
        }

        return null;
    }

    public function getLocations($limit = 25)
    {
        return $this->filterLocations($limit)->getIterator();
    }

    public function filterLocations($maxPerPage = 25, $currentPage = 1)
    {
        $pager = new Pagerfanta(
            new LocationSearchFilterAdapter(
                new LocationQuery([
                    'filter' => new LogicalAnd(
                        [
                            new ContentId($this->id),
                            new Visibility(Visibility::VISIBLE),
                        ]
                    ),
                    'sortClauses' => [
                        new Path(),
                    ],
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function getFieldRelation($fieldDefinitionIdentifier)
    {
        return $this->site->getRelationService()->loadFieldRelation(
            $this->id,
            $fieldDefinitionIdentifier
        );
    }

    public function getFieldRelations($fieldDefinitionIdentifier, $limit = 25)
    {
        $relations = $this->site->getRelationService()->loadFieldRelations(
            $this->id,
            $fieldDefinitionIdentifier
        );

        return array_slice($relations, 0, $limit);
    }

    public function filterFieldRelations(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        $maxPerPage = 25,
        $currentPage = 1
    ) {
        $relations = $this->site->getRelationService()->loadFieldRelations(
            $this->id,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        $pager = new Pagerfanta(new ArrayAdapter($relations));

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    private function initializeFields()
    {
        if ($this->fields === null) {
            $this->fields = [];
            foreach ($this->getInnerContent()->getFieldsByLanguage($this->languageCode) as $apiField) {
                $field = $this->domainObjectMapper->mapField($apiField, $this);
                $this->fields[$field->fieldDefIdentifier] = $field;
                $this->fieldsById[$field->id] = $field;
            }
        }
    }

    private function getMainLocation()
    {
        if ($this->internalMainLocation === null && $this->mainLocationId !== null) {
            $this->internalMainLocation = $this->site->getLoadService()->loadLocation(
                $this->mainLocationId
            );
        }

        return $this->internalMainLocation;
    }

    private function getInnerContent()
    {
        if ($this->innerContent === null) {
            $this->innerContent = $this->contentService->loadContent(
                $this->id,
                [$this->languageCode],
                $this->innerVersionInfo->versionNo
            );
        }

        return $this->innerContent;
    }

    private function getContentInfo()
    {
        if ($this->contentInfo === null) {
            $this->contentInfo = $this->domainObjectMapper->mapContentInfo(
                $this->innerVersionInfo,
                $this->languageCode
            );
        }

        return $this->contentInfo;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function getOwner()
    {
        if ($this->isOwnerInitialized) {
            return $this->owner;
        }

        $this->owner = $this->repository->sudo(
            function(Repository $repository) {
                try {
                    return $this->site->getLoadService()
                        ->loadContent($this->getContentInfo()->ownerId);
                } catch (NotFoundException $e) {
                    // Do nothing
                }
            }
        );

        $this->isOwnerInitialized = true;

        return $this->owner;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    private function getInnerOwnerUser()
    {
        if ($this->isInnerOwnerUserInitialized) {
            return $this->innerOwnerUser;
        }

        try {
            $this->innerOwnerUser = $this->userService->loadUser($this->getContentInfo()->ownerId);
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $this->isInnerOwnerUserInitialized = true;

        return $this->innerOwnerUser;
    }

    public function getReverseFieldRelation(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:ReverseFieldRelations');
        $query = $queryType->getQuery([
            'content' => $this,
            'field_definition_identifier' => $fieldDefinitionIdentifier,
            'content_type_identifiers' => $contentTypeIdentifiers,
            'offset' => 0,
            'limit' => 1,
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);

        if ($searchResult->totalCount === 0) {
            return null;
        }

        return $searchResult->searchHits[0]->valueObject;
    }

    public function getReverseFieldRelations(
        $fieldDefinitionIdentifier,
        $limit = 25,
        array $sortClauses = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:ReverseFieldRelations');
        $query = $queryType->getQuery([
            'content' => $this,
            'field_definition_identifier' => $fieldDefinitionIdentifier,
            'offset' => 0,
            'limit' => $limit,
            'sort_clauses' => $sortClauses,
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);

        return $this->extractValueObjects($searchResult);
    }

    public function filterReverseFieldRelations(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        $maxPerPage = 25,
        $currentPage = 1,
        array $sortClauses = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:ReverseFieldRelations');
        $pager = new Pagerfanta(
            new ContentSearchFilterAdapter(
                $queryType->getQuery([
                    'content' => $this,
                    'field_definition_identifier' => $fieldDefinitionIdentifier,
                    'content_type_identifiers' => $contentTypeIdentifiers,
                    'sort_clauses' => $sortClauses,
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function getTagRelations($limit = 25, array $sortClauses = [])
    {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:TagRelations');
        $query = $queryType->getQuery([
            'use_pager' => false,
            'content' => $this,
            'offset' => 0,
            'limit' => $limit,
            'sort_clauses' => $sortClauses,
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);

        return $this->extractValueObjects($searchResult);
    }

    public function filterTagRelations(
        array $contentTypeIdentifiers = [],
        $maxPerPage = 25,
        $currentPage = 1,
        array $sortClauses = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:TagRelations');
        $pager = new Pagerfanta(
            new ContentSearchFilterAdapter(
                $queryType->getQuery([
                    'content' => $this,
                    'content_type_identifiers' => $contentTypeIdentifiers,
                    'sort_clauses' => $sortClauses,
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function getTagFieldRelation(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:TagFieldRelations');
        $query = $queryType->getQuery([
            'use_pager' => false,
            'content' => $this,
            'field_definition_identifier' => $fieldDefinitionIdentifier,
            'content_type_identifiers' => $contentTypeIdentifiers,
            'offset' => 0,
            'limit' => 1,
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);

        if ($searchResult->totalCount === 0) {
            return null;
        }

        return $searchResult->searchHits[0]->valueObject;
    }

    public function getTagFieldRelations(
        $fieldDefinitionIdentifier,
        $limit = 25,
        array $sortClauses = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:TagFieldRelations');
        $query = $queryType->getQuery([
            'use_pager' => false,
            'content' => $this,
            'field_definition_identifier' => $fieldDefinitionIdentifier,
            'offset' => 0,
            'limit' => $limit,
            'sort_clauses' => $sortClauses,
        ]);

        $searchResult = $this->site->getFilterService()->filterContent($query);

        return $this->extractValueObjects($searchResult);
    }

    public function filterTagFieldRelations(
        $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        $maxPerPage = 25,
        $currentPage = 1,
        array $sortClauses = []
    ) {
        $queryType = $this->queryTypeRegistry->getQueryType('SiteAPI:TagFieldRelations');
        $pager = new Pagerfanta(
            new ContentSearchFilterAdapter(
                $queryType->getQuery([
                    'content' => $this,
                    'field_definition_identifier' => $fieldDefinitionIdentifier,
                    'content_type_identifiers' => $contentTypeIdentifiers,
                    'sort_clauses' => $sortClauses,
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }
}
