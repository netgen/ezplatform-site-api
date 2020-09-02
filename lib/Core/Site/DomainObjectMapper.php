<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\API\Repository\Values\Content\Location as RepoLocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\API\Values\Field as APIField;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\ContentInfo;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Psr\Log\LoggerInterface;

/**
 * @internal
 *
 * Domain object mapper is an internal service that maps eZ Platform Repository objects
 * to the native domain objects
 */
final class DomainObjectMapper
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    /**
     * @var bool
     */
    private $failOnMissingFields;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        SiteInterface $site,
        Repository $repository,
        bool $failOnMissingFields,
        LoggerInterface $logger
    ) {
        $this->site = $site;
        $this->repository = $repository;
        $this->contentTypeService = $repository->getContentTypeService();
        $this->fieldTypeService = $repository->getFieldTypeService();
        $this->failOnMissingFields = $failOnMissingFields;
        $this->logger = $logger;
    }

    /**
     * Maps Repository Content to the Site Content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Content
     */
    public function mapContent(VersionInfo $versionInfo, string $languageCode): Content
    {
        $contentInfo = $versionInfo->contentInfo;

        return new Content(
            [
                'id' => $contentInfo->id,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
                'innerVersionInfo' => $versionInfo,
                'site' => $this->site,
                'domainObjectMapper' => $this,
                'repository' => $this->repository,
            ],
            $this->failOnMissingFields,
            $this->logger
        );
    }

    /**
     * Maps Repository ContentInfo to the Site ContentInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\ContentInfo
     */
    public function mapContentInfo(VersionInfo $versionInfo, string $languageCode): ContentInfo
    {
        $contentInfo = $versionInfo->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

        return new ContentInfo(
            [
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
                'contentTypeIdentifier' => $contentType->identifier,
                'contentTypeName' => $this->getTranslatedString($languageCode, (array) $contentType->getNames()),
                'contentTypeDescription' => $this->getTranslatedString($languageCode, (array) $contentType->getDescriptions()),
                'innerContentInfo' => $versionInfo->contentInfo,
                'innerContentType' => $contentType,
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository Location to the Site Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Location
     */
    public function mapLocation(RepoLocation $location, VersionInfo $versionInfo, string $languageCode): Location
    {
        return new Location(
            [
                'innerLocation' => $location,
                'languageCode' => $languageCode,
                'innerVersionInfo' => $versionInfo,
                'site' => $this->site,
                'domainObjectMapper' => $this,
            ],
            $this->logger
        );
    }

    /**
     * Maps Repository Field to the Site Field.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $apiField
     * @param \Netgen\EzPlatformSiteApi\API\Values\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Field
     */
    public function mapField(RepoField $apiField, SiteContent $content): APIField
    {
        $fieldDefinition = $content->contentInfo->innerContentType->getFieldDefinition($apiField->fieldDefIdentifier);
        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $isEmpty = $this->fieldTypeService->getFieldType($fieldTypeIdentifier)->isEmptyValue(
            $apiField->value
        );

        return new Field([
            'id' => $apiField->id,
            'fieldDefIdentifier' => $fieldDefinition->identifier,
            'value' => $apiField->value,
            'languageCode' => $apiField->languageCode,
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
            'name' => $this->getTranslatedString(
                $content->languageCode,
                (array) $fieldDefinition->getNames()
            ),
            'description' => $this->getTranslatedString(
                $content->languageCode,
                (array) $fieldDefinition->getDescriptions()
            ),
            'content' => $content,
            'innerField' => $apiField,
            'innerFieldDefinition' => $fieldDefinition,
            'isEmpty' => $isEmpty,
            'isSurrogate' => false,
        ]);
    }

    private function getTranslatedString(string $languageCode, array $strings)
    {
        if (\array_key_exists($languageCode, $strings)) {
            return $strings[$languageCode];
        }

        return null;
    }
}
