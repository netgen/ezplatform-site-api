<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\API\Values;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Site ContentInfo object provides meta information of the Site Content object.
 *
 * Corresponds to eZ Platform Repository ContentInfo object.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\ContentInfo
 *
 * @property int|string $id
 * @property int|string $contentTypeId
 * @property int|string $sectionId
 * @property int $currentVersionNo
 * @property bool $published
 * @property int|string $ownerId
 * @property \DateTime $modificationDate
 * @property \DateTime $publishedDate
 * @property bool $alwaysAvailable
 * @property string $remoteId
 * @property string $mainLanguageCode
 * @property int|string $mainLocationId
 * @property string $name
 * @property string $languageCode
 * @property string $contentTypeIdentifier
 * @property string $contentTypeName
 * @property string $contentTypeDescription
 * @property \eZ\Publish\API\Repository\Values\Content\ContentInfo $innerContentInfo
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentType $innerContentType
 * @property null|\Netgen\EzPlatformSiteApi\API\Values\Location $mainLocation
 */
abstract class ContentInfo extends ValueObject
{
}
