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
 * @property int $id
 * @property int $contentTypeId
 * @property int $sectionId
 * @property int $currentVersionNo
 * @property bool $published
 * @property bool $isHidden
 * @property bool $isVisible
 * @property int $ownerId
 * @property \DateTime $modificationDate
 * @property \DateTime $publishedDate
 * @property bool $alwaysAvailable
 * @property string $remoteId
 * @property string $mainLanguageCode
 * @property int $mainLocationId
 * @property string $name
 * @property string $languageCode
 * @property string $contentTypeIdentifier
 * @property string $contentTypeName
 * @property string $contentTypeDescription
 * @property \eZ\Publish\API\Repository\Values\Content\ContentInfo $innerContentInfo
 * @property \eZ\Publish\API\Repository\Values\ContentType\ContentType $innerContentType
 * @property \Netgen\EzPlatformSiteApi\API\Values\Location|null $mainLocation
 */
abstract class ContentInfo extends ValueObject
{
}
