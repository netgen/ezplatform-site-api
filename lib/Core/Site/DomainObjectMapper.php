<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Field as APIField;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition as CoreFieldDefinition;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\ContentInfo;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field\NullValue;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Psr\Log\LoggerInterface;
use RuntimeException;

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
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param bool $failOnMissingFields
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        SiteInterface $site,
        Repository $repository,
        $failOnMissingFields,
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
    public function mapContent(VersionInfo $versionInfo, $languageCode)
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
     * @return \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    public function mapContentInfo(VersionInfo $versionInfo, $languageCode)
    {
        $contentInfo = $versionInfo->contentInfo;
        $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);

        return new ContentInfo(
            [
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
                'contentTypeIdentifier' => $contentType->identifier,
                'contentTypeName' => $this->getTranslatedString($languageCode, (array)$contentType->getNames()),
                'contentTypeDescription' => $this->getTranslatedString($languageCode, (array)$contentType->getDescriptions()),
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
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function mapLocation(APILocation $location, VersionInfo $versionInfo, $languageCode)
    {
        return new Location(
            [
                'innerLocation' => $location,
                'languageCode' => $languageCode,
                'innerVersionInfo' => $versionInfo,
                'site' => $this->site,
                'domainObjectMapper' => $this,
            ]
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
    public function mapField(APIField $apiField, SiteContent $content)
    {
        $fieldDefinition = $content->contentInfo->innerContentType->getFieldDefinition($apiField->fieldDefIdentifier);

        if (!$fieldDefinition instanceof FieldDefinition) {
            $message = sprintf(
                'Field "%s" in Content #%s does not have a FieldDefinition',
                $apiField->fieldDefIdentifier,
                $content->id
            );

            if ($this->failOnMissingFields) {
                throw new RuntimeException($message);
            }

            $this->logger->critical($message . ', using null field instead');

            return $this->getNullField($apiField->fieldDefIdentifier, $content);
        }

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
                (array)$fieldDefinition->getNames()
            ),
            'description' => $this->getTranslatedString(
                $content->languageCode,
                (array)$fieldDefinition->getDescriptions()
            ),
            'content' => $content,
            'innerField' => $apiField,
            'innerFieldDefinition' => $fieldDefinition,
            'isEmpty' => $isEmpty,
        ]);
    }

    public function getNullField($identifier, SiteContent $content)
    {
        $apiField = new APIField([
            'id' => 0,
            'fieldDefIdentifier' => $identifier,
            'value' => new NullValue(),
            'languageCode' => $content->languageCode,
            'fieldTypeIdentifier' => 'ngnull',
        ]);

        $fieldDefinition = new CoreFieldDefinition([
            'id' => 0,
            'identifier' => $apiField->fieldDefIdentifier,
            'fieldGroup' => '',
            'position' => 0,
            'fieldTypeIdentifier' => $apiField->fieldTypeIdentifier,
            'isTranslatable' => false,
            'isRequired' => false,
            'isInfoCollector' => false,
            'defaultValue' => null,
            'isSearchable' => false,
            'mainLanguageCode' => $apiField->languageCode,
            'fieldSettings' => [],
            'validatorConfiguration' => [],
        ]);

        return new Field([
            'id' => $apiField->id,
            'fieldDefIdentifier' => $fieldDefinition->identifier,
            'value' => $apiField->value,
            'languageCode' => $apiField->languageCode,
            'fieldTypeIdentifier' => $apiField->fieldTypeIdentifier,
            'name' => '',
            'description' => '',
            'content' => $content,
            'innerField' => $apiField,
            'innerFieldDefinition' => $fieldDefinition,
            'isEmpty' => true,
        ]);
    }

    private function getTranslatedString($languageCode, $strings)
    {
        if (array_key_exists($languageCode, $strings)) {
            return $strings[$languageCode];
        }

        return null;
    }
}
