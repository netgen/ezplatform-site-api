<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

/**
 * Null field type relation Resolver.
 *
 * This resolver will match field type with identifier 'ngnull', returned when nonexistent field is
 * requested from Content.
 */
class NgNull extends Resolver
{
    protected function getSupportedFieldTypeIdentifier(): string
    {
        return 'ngnull';
    }

    protected function getRelationIdsFromValue(Value $value)
    {
        return [];
    }
}
