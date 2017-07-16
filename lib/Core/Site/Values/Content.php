<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;

final class Content extends APIContent
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    protected $contentInfo;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    protected $fields = [];

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $innerContent;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Field[]
     */
    private $fieldsById = [];

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalLocations;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location[]
     */
    private $internalMainLocation;

    public function __construct(array $properties = [])
    {
        if (isset($properties['_fields_data'])) {
            foreach ($properties['_fields_data'] as $fieldData) {
                $this->buildField($fieldData);
            }

            unset($properties['_fields_data']);
        }

        if (array_key_exists('site', $properties)) {
            $this->site = $properties['site'];
            unset($properties['site']);
        }

        parent::__construct($properties);
    }

    public function hasField($identifier)
    {
        return isset($this->fields[$identifier]);
    }

    public function getField($identifier)
    {
        if ($this->hasField($identifier)) {
            return $this->fields[$identifier];
        }

        return null;
    }

    public function hasFieldById($id)
    {
        return isset($this->fieldsById[$id]);
    }

    public function getFieldById($id)
    {
        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id];
        }

        return null;
    }

    public function getFieldValue($identifier)
    {
        if ($this->hasField($identifier)) {
            return $this->fields[$identifier]->value;
        }

        return null;
    }

    public function getFieldValueById($id)
    {
        if ($this->hasFieldById($id)) {
            return $this->fieldsById[$id]->value;
        }

        return null;
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
            case 'id':
                return $this->contentInfo->id;
            case 'name':
                return $this->contentInfo->name;
            case 'mainLocationId':
                return $this->contentInfo->mainLocationId;
            case 'locations':
                return $this->getLocations();
            case 'mainLocation':
                return $this->getMainLocation();
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (property_exists($this->innerContent, $property)) {
            return $this->innerContent->$property;
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
            case 'name':
            case 'mainLocationId':
            case 'locations':
            case 'mainLocation':
                return true;
        }

        if (property_exists($this, $property) || property_exists($this->innerContent, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    private function buildField(array $properties = [])
    {
        $properties['content'] = $this;
        $field = new Field($properties);

        $this->fields[$field->fieldDefIdentifier] = $field;
        $this->fieldsById[$field->id] = $field;
    }

    private function getLocations()
    {
        if ($this->internalLocations === null) {
            $this->internalLocations = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        //
                    ]
                )
            );
        }

        return $this->internalLocations;
    }

    private function getMainLocation()
    {
        if ($this->internalMainLocation === null) {
            $this->internalMainLocation = $this->site->getFindService()->findLocations(
                new LocationQuery(
                    [
                        //
                    ]
                )
            );
        }

        if (!empty($this->internalMainLocation)) {
            return $this->internalMainLocation[0];
        }

        return null;
    }
}
