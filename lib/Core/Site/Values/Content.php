<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content as RepoContent;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;
use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;

/**
 * @internal hint against API Content instead of this class
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Content
 */
final class Content extends APIContent
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
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
     * @var \Netgen\EzPlatformSiteApi\API\Values\Fields
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
     * @var APILocation
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

    public function __construct(array $properties, bool $failOnMissingFields, LoggerInterface $logger)
    {
        $this->site = $properties['site'];
        $this->domainObjectMapper = $properties['domainObjectMapper'];
        $this->contentService = $properties['repository']->getContentService();
        $this->userService = $properties['repository']->getUserService();
        $this->repository = $properties['repository'];
        $this->fields = new Fields($this, $this->domainObjectMapper, $failOnMissingFields, $logger);

        unset(
            $properties['site'],
            $properties['domainObjectMapper'],
            $properties['repository']
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
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'fields':
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
    public function __isset($property): bool
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

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function __debugInfo(): array
    {
        return [
            'id' => $this->id,
            'mainLocationId' => $this->mainLocationId,
            'name' => $this->name,
            'languageCode' => $this->languageCode,
            'contentInfo' => $this->getContentInfo(),
            'fields' => $this->fields,
            'mainLocation' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
            'innerContent' => '[An instance of eZ\Publish\API\Repository\Values\Content\Content]',
            'innerVersionInfo' => '[An instance of eZ\Publish\API\Repository\Values\Content\VersionInfo]',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function hasField(string $identifier): bool
    {
        return $this->fields->hasField($identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getField(string $identifier): APIField
    {
        return $this->fields->getField($identifier);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function hasFieldById($id): bool
    {
        return $this->fields->hasFieldById($id);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFieldById($id): APIField
    {
        return $this->fields->getFieldById($id);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFirstNonEmptyField(string $firstIdentifier, string ...$otherIdentifiers): APIField
    {
        return $this->fields->getFirstNonEmptyField($firstIdentifier, ...$otherIdentifiers);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFieldValue(string $identifier): Value
    {
        return $this->getField($identifier)->value;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function getFieldValueById($id): Value
    {
        return $this->getFieldById($id)->value;
    }

    public function getLocations(int $limit = 25): array
    {
        return $this->filterLocations($limit)->getIterator()->getArrayCopy();
    }

    public function filterLocations(int $maxPerPage = 25, int $currentPage = 1): Pagerfanta
    {
        $pager = new Pagerfanta(
            new FilterAdapter(
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

    public function getFieldRelation(string $fieldDefinitionIdentifier): ?APIContent
    {
        return $this->site->getRelationService()->loadFieldRelation(
            $this,
            $fieldDefinitionIdentifier
        );
    }

    public function getFieldRelations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return $this->site->getRelationService()->loadFieldRelations(
            $this,
            $fieldDefinitionIdentifier,
            [],
            $limit
        );
    }

    public function filterFieldRelations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1
    ): Pagerfanta {
        $relations = $this->site->getRelationService()->loadFieldRelations(
            $this,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        $pager = new Pagerfanta(new ArrayAdapter($relations));

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    public function getFieldRelationLocation(string $fieldDefinitionIdentifier): ?APILocation
    {
        return $this->site->getRelationService()->loadFieldRelationLocation(
            $this,
            $fieldDefinitionIdentifier
        );
    }

    public function getFieldRelationLocations(string $fieldDefinitionIdentifier, int $limit = 25): array
    {
        return $this->site->getRelationService()->loadFieldRelationLocations(
            $this,
            $fieldDefinitionIdentifier,
            [],
            $limit
        );
    }

    public function filterFieldRelationLocations(
        string $fieldDefinitionIdentifier,
        array $contentTypeIdentifiers = [],
        int $maxPerPage = 25,
        int $currentPage = 1
    ): Pagerfanta {
        $relations = $this->site->getRelationService()->loadFieldRelationLocations(
            $this,
            $fieldDefinitionIdentifier,
            $contentTypeIdentifiers
        );

        $pager = new Pagerfanta(new ArrayAdapter($relations));

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    private function getMainLocation(): ?APILocation
    {
        if ($this->internalMainLocation === null && $this->mainLocationId !== null) {
            $this->internalMainLocation = $this->site->getLoadService()->loadLocation(
                $this->mainLocationId
            );
        }

        return $this->internalMainLocation;
    }

    private function getInnerContent(): RepoContent
    {
        if ($this->innerContent === null) {
            $this->innerContent = $this->repository->sudo(
                function (Repository $repository): RepoContent {
                    return $this->contentService->loadContent(
                        $this->id,
                        [$this->languageCode],
                        $this->innerVersionInfo->versionNo
                    );
                }
            );
        }

        return $this->innerContent;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    private function getContentInfo(): APIContentInfo
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
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private function getOwner(): ?APIContent
    {
        if ($this->isOwnerInitialized) {
            return $this->owner;
        }

        $this->owner = $this->repository->sudo(
            function (Repository $repository): ?APIContent {
                try {
                    return $this->site->getLoadService()->loadContent($this->getContentInfo()->ownerId);
                } catch (NotFoundException $e) {
                    // Do nothing
                }

                return null;
            }
        );

        $this->isOwnerInitialized = true;

        return $this->owner;
    }

    /**
     * @return null|\eZ\Publish\API\Repository\Values\User\User
     */
    private function getInnerOwnerUser(): ?User
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
}
