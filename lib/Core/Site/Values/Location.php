<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalNot;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ParentLocationId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use eZ\Publish\API\Repository\Exceptions\NotImplementedException;
use Psr\Log\LoggerInterface;
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
     * @var string
     */
    private $languageCode;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    private $innerVersionInfo;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private $domainObjectMapper;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalParent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $internalContent;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(array $properties = [], LoggerInterface $logger)
    {
        $this->site = $properties['site'];
        $this->domainObjectMapper = $properties['domainObjectMapper'];
        $this->innerVersionInfo = $properties['innerVersionInfo'];
        $this->languageCode = $properties['languageCode'];
        $this->logger = $logger;

        unset(
            $properties['site'],
            $properties['domainObjectMapper'],
            $properties['innerVersionInfo'],
            $properties['languageCode']
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
            case 'contentId':
                return $this->innerLocation->contentId;
            case 'parent':
                return $this->getParent();
            case 'content':
                return $this->getContent();
            case 'contentInfo':
                return $this->getContentInfo();
        }

        if (\property_exists($this, $property)) {
            return $this->{$property};
        }

        if (\property_exists($this->innerLocation, $property)) {
            return $this->innerLocation->{$property};
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
            case 'contentId':
            case 'parent':
            case 'content':
                return true;
        }

        if (\property_exists($this, $property) || \property_exists($this->innerLocation, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function __debugInfo(): array
    {
        return [
            'id' => $this->innerLocation->id,
            'status' => $this->innerLocation->status,
            'priority' => $this->innerLocation->priority,
            'hidden' => $this->innerLocation->hidden,
            'invisible' => $this->innerLocation->invisible,
            'remoteId' => $this->innerLocation->remoteId,
            'parentLocationId' => $this->innerLocation->parentLocationId,
            'pathString' => $this->innerLocation->pathString,
            'path' => $this->innerLocation->path,
            'depth' => $this->innerLocation->depth,
            'sortField' => $this->innerLocation->sortField,
            'sortOrder' => $this->innerLocation->sortOrder,
            'contentId' => $this->innerLocation->contentId,
            'innerLocation' => '[An instance of eZ\Publish\API\Repository\Values\Content\Location]',
            'contentInfo' => $this->getContentInfo(),
            'parent' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
            'content' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Content]',
        ];
    }

    public function getChildren(int $limit = 25): array
    {
        return $this->filterChildren([], $limit)->getIterator()->getArrayCopy();
    }

    public function filterChildren(array $contentTypeIdentifiers = [], int $maxPerPage = 25, int $currentPage = 1): Pagerfanta
    {
        $criteria = [
            new ParentLocationId($this->id),
            new Visibility(Visibility::VISIBLE),
        ];

        if (!empty($contentTypeIdentifiers)) {
            $criteria[] = new ContentTypeIdentifier($contentTypeIdentifiers);
        }

        return $this->getFilterPager($criteria, $maxPerPage, $currentPage);
    }

    public function getFirstChild(?string $contentTypeIdentifier = null): ?APILocation
    {
        $contentTypeIdentifiers = [];

        if ($contentTypeIdentifier !== null) {
            $contentTypeIdentifiers = [$contentTypeIdentifier];
        }

        $pager = $this->filterChildren($contentTypeIdentifiers, 1);

        if ($pager->count() > 0) {
            return $pager->getIterator()->current();
        }

        return null;
    }

    public function getSiblings(int $limit = 25): array
    {
        return $this->filterSiblings([], $limit)->getIterator()->getArrayCopy();
    }

    public function filterSiblings(array $contentTypeIdentifiers = [], int $maxPerPage = 25, int $currentPage = 1): Pagerfanta
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

        return $this->getFilterPager($criteria, $maxPerPage, $currentPage);
    }

    private function getFilterPager(array $criteria, int $maxPerPage = 25, int $currentPage = 1): Pagerfanta
    {
        try {
            $sortClauses = $this->innerLocation->getSortClauses();
        } catch (NotImplementedException $e) {
            $this->logger->notice("Cannot use sort clauses from parent location: {$e->getMessage()}");

            $sortClauses = [];
        }

        $pager = new Pagerfanta(
            new FilterAdapter(
                new LocationQuery([
                    'filter' => new LogicalAnd($criteria),
                    'sortClauses' => $sortClauses,
                ]),
                $this->site->getFilterService()
            )
        );

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($maxPerPage);
        $pager->setCurrentPage($currentPage);

        return $pager;
    }

    private function getParent(): APILocation
    {
        if ($this->internalParent === null) {
            $this->internalParent = $this->site->getLoadService()->loadLocation(
                $this->parentLocationId
            );
        }

        return $this->internalParent;
    }

    private function getContent(): APIContent
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->domainObjectMapper->mapContent(
                $this->innerVersionInfo,
                $this->languageCode
            );
        }

        return $this->internalContent;
    }

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
}
