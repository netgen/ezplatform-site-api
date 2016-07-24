<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

/**
 * @internal
 */
trait ContentTrait
{
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
        if ($property === 'id') {
            return true;
        }

        if ($property === 'name') {
            return true;
        }

        if ($property === 'mainLocationId') {
            return true;
        }

        return parent::__isset($property);
    }
}
