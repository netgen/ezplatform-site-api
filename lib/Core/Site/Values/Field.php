<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;

final class Field extends APIField
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $fieldTypeIdentifier;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected $content;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field
     */
    protected $innerField;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    protected $innerFieldDefinition;

    /**
     * @var bool
     */
    private $isEmpty;

    public function __construct(array $properties = [])
    {
        if (isset($properties['isEmpty'])) {
            $this->isEmpty = $properties['isEmpty'];

            unset($properties['isEmpty']);
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
        if (property_exists($this->innerField, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function isEmpty()
    {
        return $this->isEmpty;
    }
}
