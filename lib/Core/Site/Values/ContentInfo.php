<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

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
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        } elseif (property_exists($this->innerContentInfo, $property)) {
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
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerContentInfo, $property)) {
            return true;
        }

        return parent::__isset($property);
    }
}
