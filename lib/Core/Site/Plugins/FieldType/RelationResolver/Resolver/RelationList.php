<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

/**
 * RelationList field type relation resolver.
 *
 * @see \eZ\Publish\Core\FieldType\RelationList
 */
class RelationList extends Resolver
{
    protected function getSupportedFieldTypeIdentifier()
    {
        return 'ezobjectrelationlist';
    }

    protected function getRelationIdsFromValue(Value $value)
    {
        /** @var \eZ\Publish\Core\FieldType\RelationList\Value $value */
        return $value->destinationContentIds;
    }
}
