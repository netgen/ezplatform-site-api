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
        if (isset($properties['_fields_data'])) {
            foreach ($properties['_fields_data'] as $fieldData) {
                $this->buildField($fieldData);
            }

            unset($properties['_fields_data']);
        }

        parent::__construct($properties);
    }
}
