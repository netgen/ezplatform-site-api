<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;

final class Field extends APIField
{
    use TranslatableTrait;

    /**
     * @var bool
     */
    private $isEmpty;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $innerField;

    public function __construct(array $properties = [])
    {
        if (isset($properties['isEmpty'])) {
            $this->isEmpty = $properties['isEmpty'];

            unset($properties['isEmpty']);
        }

        parent::__construct($properties);
    }

    public function isEmpty()
    {
        return $this->isEmpty;
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
            case 'fieldTypeIdentifier':
                return $this->innerFieldDefinition->fieldTypeIdentifier;
            case 'innerFieldDefinition':
                return $this->content->contentInfo->innerContentType->getFieldDefinition(
                    $this->innerField->fieldDefIdentifier
                );
            case 'name':
                return $this->getTranslatedString(
                    $this->content->contentInfo->languageCode,
                    (array)$this->innerFieldDefinition->getNames()
                );
            case 'description':
                return $this->getTranslatedString(
                    $this->content->contentInfo->languageCode,
                    (array)$this->innerFieldDefinition->getDescriptions()
                );
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->innerField, $property)) {
            return $this->innerField->$property;
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
            case 'fieldTypeIdentifier':
            case 'innerFieldDefinition':
            case 'name':
            case 'description':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerField, $property)) {
            return true;
        }

        return parent::__isset($property);
    }
}
