<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

use Netgen\EzPlatformSite\API\Values\Field as APIField;

final class Field extends APIField
{
    /**
     * @var bool
     */
    private $isEmpty;

    /**
     * @var \Netgen\EzPlatformSite\API\Values\Content
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
            case 'id':
                return $this->innerField->id;
            case 'identifier':
                return $this->innerField->fieldDefIdentifier;
            case 'fieldTypeIdentifier':
                return $this->innerFieldDefinition->fieldTypeIdentifier;
            case 'value':
                return $this->innerField->value;
            case 'innerFieldDefinition':
                return $this->content->contentInfo->innerContentType->getFieldDefinition(
                    $this->identifier
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
            case 'id':
            case 'identifier':
            case 'fieldTypeIdentifier':
            case 'value':
            case 'innerFieldDefinition':
            case 'name':
            case 'description':
                return true;
        }

        return parent::__isset($property);
    }

    private function getTranslatedString($languageCode, $strings)
    {
        if (array_key_exists($languageCode, $strings)) {
            return $strings[$languageCode];
        }

        return null;
    }
}
