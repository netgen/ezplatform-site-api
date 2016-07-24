<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

use Netgen\EzPlatformSite\API\Values\Node as APINode;

final class Node extends APINode
{
    use ContentTrait;

    /**
     * @var \Netgen\EzPlatformSite\API\Values\Location
     */
    protected $location;

    public function __construct(array $properties = [])
    {
        if (isset($properties['fieldsById'])) {
            $this->fieldsById = $properties['fieldsById'];
            unset($properties['fieldsById']);
        }

        parent::__construct($properties);
    }
}
