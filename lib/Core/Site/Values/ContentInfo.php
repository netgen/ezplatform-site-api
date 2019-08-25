<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site\Values;

use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;
use Netgen\EzPlatformSiteApi\API\Values\Location as APILocation;

final class ContentInfo extends APIContentInfo
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $languageCode;

    /**
     * @var int|string
     */
    protected $contentTypeIdentifier;

    /**
     * @var string
     */
    protected $contentTypeName;

    /**
     * @var string
     */
    protected $contentTypeDescription;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    protected $innerContentInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\ContentType
     */
    protected $innerContentType;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private $internalMainLocation;

    public function __construct(array $properties = [])
    {
        if (\array_key_exists('site', $properties)) {
            $this->site = $properties['site'];
            unset($properties['site']);
        }

        parent::__construct($properties);
    }

    /**
     * {@inheritdoc}
     *
     * Magic getter for retrieving convenience properties.
     *
     * @param string $property The name of the property to retrieve
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($property === 'mainLocation') {
            return $this->getMainLocation();
        }

        if (\property_exists($this, $property)) {
            return $this->{$property};
        }

        if (\property_exists($this->innerContentInfo, $property)) {
            return $this->innerContentInfo->{$property};
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
    public function __isset($property): bool
    {
        if ($property === 'mainLocation') {
            return true;
        }

        if (\property_exists($this, $property) || \property_exists($this->innerContentInfo, $property)) {
            return true;
        }

        return parent::__isset($property);
    }

    public function __debugInfo(): array
    {
        return [
            'id' => $this->innerContentInfo->id,
            'contentTypeId' => $this->innerContentInfo->contentTypeId,
            'sectionId' => $this->innerContentInfo->sectionId,
            'currentVersionNo' => $this->innerContentInfo->currentVersionNo,
            'published' => $this->innerContentInfo->published,
            'ownerId' => $this->innerContentInfo->ownerId,
            'modificationDate' => $this->innerContentInfo->modificationDate,
            'publishedDate' => $this->innerContentInfo->publishedDate,
            'alwaysAvailable' => $this->innerContentInfo->alwaysAvailable,
            'remoteId' => $this->innerContentInfo->remoteId,
            'mainLanguageCode' => $this->innerContentInfo->mainLanguageCode,
            'mainLocationId' => $this->innerContentInfo->mainLocationId,
            'name' => $this->name,
            'languageCode' => $this->languageCode,
            'contentTypeIdentifier' => $this->contentTypeIdentifier,
            'contentTypeName' => $this->contentTypeName,
            'contentTypeDescription' => $this->contentTypeDescription,
            'innerContentInfo' => '[An instance of eZ\Publish\API\Repository\Values\Content\ContentInfo]',
            'innerContentType' => '[An instance of eZ\Publish\API\Repository\Values\ContentType\ContentType]',
            'mainLocation' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
        ];
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return null|\Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getMainLocation(): ?APILocation
    {
        if ($this->internalMainLocation === null && $this->mainLocationId !== null) {
            $this->internalMainLocation = $this->site->getLoadService()->loadLocation(
                $this->innerContentInfo->mainLocationId
            );
        }

        return $this->internalMainLocation;
    }
}
