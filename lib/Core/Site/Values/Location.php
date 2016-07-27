<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

use Netgen\EzPlatformSite\API\Values\Location as APILocation;

final class Location extends APILocation
{
    /**
     * @var \Netgen\EzPlatformSite\API\Values\ContentInfo
     */
    protected $contentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected $innerLocation;

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
                return $this->innerLocation->id;
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
                return true;
        }

        return parent::__isset($property);
    }
}
