<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

use eZ\Publish\SPI\FieldType\Value;
use Netgen\EzPlatformSiteApi\Core\Site\Plugins\FieldType\RelationResolver\Resolver;

/**
 * Relation field type relation Resolver.
 *
 * @see \eZ\Publish\Core\FieldType\Relation
 */
class Relation extends Resolver
{
    protected function getSupportedFieldTypeIdentifier(): string
    {
        return 'ezobjectrelation';
    }

    protected function getRelationIdsFromValue(Value $value): array
    {
        /* @var \eZ\Publish\Core\FieldType\Relation\Value $value */
        if (null === $value->destinationContentId) {
            return [];
        }

        return [$value->destinationContentId];
    }
}
