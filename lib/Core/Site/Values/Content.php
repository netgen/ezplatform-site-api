<?php

namespace Netgen\EzPlatformSite\Core\Site\Values;

use Netgen\EzPlatformSite\API\Values\Content as APIContent;

final class Content extends APIContent
{
    use ContentTrait;

    public function __construct(array $properties = [])
    {
        if (isset($properties['fieldsById'])) {
            $this->fieldsById = $properties['fieldsById'];
            unset($properties['fieldsById']);
        }

        parent::__construct($properties);
    }
}
