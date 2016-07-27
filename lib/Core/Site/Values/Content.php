<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

use Netgen\EzPlatformSite\API\Values\Content as APIContent;

final class Content extends APIContent
{
    use ContentTrait;

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
