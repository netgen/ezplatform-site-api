<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Visibility;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\Location\Path;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;
use Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta\FilterAdapter;
use Pagerfanta\Pagerfanta;

final class ContentInfo extends APIContentInfo
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var string|int
     */
    protected $contentTypeIdentifier;

    /**
     * @var string
     */
    protected $contentTypeName;

    /**
     * @var string
     */
    protected $contentTypeDescription;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    protected $innerContentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected $innerContentType;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $internalContent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalMainLocation;

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
            case 'mainLocation':
                return $this->getMainLocation();
            case 'content':
                return $this->getContent();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->innerContentInfo, $property)) {
            return $this->innerContentInfo->$property;
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
            case 'mainLocation':
            case 'content':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerContentInfo, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function __debugInfo()
    {
        return [
            'id' => $this->innerContentInfo->id,
            'contentTypeId' => $this->innerContentInfo->contentTypeId,
            'sectionId' => $this->innerContentInfo->sectionId,
            'currentVersionNo' => $this->innerContentInfo->currentVersionNo,
            'published' => $this->innerContentInfo->published,
            'ownerId' => $this->innerContentInfo->ownerId,
            'modificationDate' => $this->innerContentInfo->modificationDate,
            'publishedDate' => $this->innerContentInfo->publishedDate,
            'alwaysAvailable' => $this->innerContentInfo->alwaysAvailable,
            'remoteId' => $this->innerContentInfo->remoteId,
            'mainLanguageCode' => $this->innerContentInfo->mainLanguageCode,
            'mainLocationId' => $this->innerContentInfo->mainLocationId,
            'name' => $this->name,
            'languageCode' => $this->languageCode,
            'contentTypeIdentifier' => $this->contentTypeIdentifier,
            'contentTypeName' => $this->contentTypeName,
            'contentTypeDescription' => $this->contentTypeDescription,
            'innerContentInfo' => '[An instance of eZ\Publish\API\Repository\Values\Content\ContentInfo]',
            'innerContentType' => '[An instance of eZ\Publish\API\Repository\Values\ContentType\ContentType]',
            'mainLocation' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
            'content' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Content]',
        ];
    }

    public function getLocations($limit = 25)
    {
        @trigger_error('getContent() is deprecated since version 2.6 and will be removed in 3.0. Use the same method on Content object instead.', E_USER_DEPRECATED);

        return $this->filterLocations($limit)->getIterator();
    }

    public function filterLocations($maxPerPage = 25, $currentPage = 1)
    {
        @trigger_error('getContent() is deprecated since version 2.6 and will be removed in 3.0. Use the same method on Content object instead.', E_USER_DEPRECATED);

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

    private function getContent()
    {
        @trigger_error('Accessing Content from ContentInfo is deprecated since version 2.6 and will be removed in 3.0. Use the Content object directly instead.', E_USER_DEPRECATED);

        if ($this->internalContent === null) {
            $this->internalContent = $this->site->getLoadService()->loadContent($this->id);
        }

        return $this->internalContent;
    }

    private function getMainLocation()
    {
        if ($this->internalMainLocation === null && $this->mainLocationId !== null) {
            $this->internalMainLocation = $this->site->getLoadService()->loadLocation(
                $this->innerContentInfo->mainLocationId
            );
        }

        return $this->internalMainLocation;
    }
}
