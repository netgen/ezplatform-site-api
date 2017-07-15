<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;

final class ContentInfo extends APIContentInfo
{
    use TranslatableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    protected $innerContentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected $innerContentType;

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
    private $internalLocations;

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
     * Magic getter for retrieving convenience properties.
     *
     * @param string $property The name of the property to retrieve
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'contentTypeIdentifier':
                return $this->innerContentType->identifier;
            case 'contentTypeName':
                return $this->getTranslatedString(
                    $this->languageCode,
                    (array)$this->innerContentType->getNames()
                );
            case 'contentTypeDescription':
                return $this->getTranslatedString(
                    $this->languageCode,
                    (array)$this->innerContentType->getDescriptions()
                );
            case 'locations':
                return $this->getLocations();
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
            case 'contentTypeIdentifier':
            case 'contentTypeName':
            case 'contentTypeDescription':
            case 'locations':
            case 'content':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerContentInfo, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function getLocations()
    {
        if ($this->internalLocations === null) {
            $this->internalLocations = $this->findService->findLocations(
                new LocationQuery(
                    [
                        //
                    ]
                )
            );
        }

        return $this->internalLocations;
    }

    public function getContent()
    {
        if ($this->internalContent === null) {
            $this->internalContent = $this->loadService->loadContent($this->innerContentInfo->id);
        }

        return $this->internalContent;
    }
}
