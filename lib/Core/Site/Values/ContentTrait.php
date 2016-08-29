<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

/**
 * @internal
 */
trait ContentTrait
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
     * @var \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    private $fieldsById = [];

    public function getField($identifier)
    {
        if (isset($this->fields[$identifier])) {
            return $this->fields[$identifier];
        }

        return null;
    }

    public function getFieldById($id)
    {
        if (isset($this->fieldsById[$id])) {
            return $this->fieldsById[$id];
        }

        return null;
    }

    public function getFieldValue($identifier)
    {
        if (isset($this->fields[$identifier])) {
            return $this->fields[$identifier]->value;
        }

        return null;
    }

    public function getFieldValueById($id)
    {
        if (isset($this->fieldsById[$id])) {
            return $this->fieldsById[$id]->value;
        }

        return null;
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
                return $this->contentInfo->id;
            case 'name':
                return $this->contentInfo->name;
            case 'mainLocationId':
                return $this->contentInfo->mainLocationId;
        }

        if (property_exists($this, $property)) {
            return $this->$property;
        } elseif (property_exists($this->innerContent, $property)) {
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
}
