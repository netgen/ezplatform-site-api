<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
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
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService
     */
    private $loadService;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalChildren;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalParent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    private $internalContent;

    public function __construct(array $properties = [])
    {
        if (isset($properties['findService'])) {
            $this->findService = $properties['findService'];
            unset($properties['findService']);
        }

        if (isset($properties['loadService'])) {
            $this->loadService = $properties['loadService'];
            unset($properties['loadService']);
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

    public function getChildren()
    {
        if ($this->internalChildren === null) {
            $this->internalChildren = $this->findService->findLocations(
                new LocationQuery(
                    [
                        //
                    ]
                )
            );
        }

        return $this->internalChildren;
    }

    public function getParent()
    {
        if ($this->internalParent === null) {
            $this->internalParent = $this->findService->findLocations(
                new LocationQuery(
                    [
                        //
                    ]
                )
            );
        }

        if (!empty($this->internalParent)) {
            return $this->internalParent[0];
        }

        return null;
    }

    public function getContent()
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->loadService->loadContent($this->contentInfo->id);
        }

        return $this->internalContent;
    }
}
