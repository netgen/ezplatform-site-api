<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\ContentInfo;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Node;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Field as APIField;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;

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
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     */
    public function __construct(
        Site $site,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService
    ) {
        $this->site = $site;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * Maps Repository Content to the Site Content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Content
     */
    public function mapContent(APIContent $content, $languageCode)
    {
        $contentType = $this->contentTypeService->loadContentType(
            $content->contentInfo->contentTypeId
        );
        $fields = $content->getFieldsByLanguage($languageCode);
        $fieldsData = [];
        foreach ($fields as $field) {
            $fieldsData[] = $this->mapFieldData($field, $contentType);
        }

        return new Content(
            [
                '_fields_data' => $fieldsData,
                'contentInfo' => $this->mapContentInfo(
                    $content->versionInfo,
                    $languageCode,
                    $contentType
                ),
                'innerContent' => $content,
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository ContentInfo to the Site ContentInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    public function mapContentInfo(
        VersionInfo $versionInfo,
        $languageCode,
        ContentType $contentType = null
    ) {
        $contentInfo = $versionInfo->contentInfo;

        if ($contentType === null) {
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        }

        return new ContentInfo(
            [
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
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
                'contentInfo' => $this->mapContentInfo($versionInfo, $languageCode),
                'innerLocation' => $location,
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository Content and Location to the Site Node.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Node
     */
    public function mapNode(APILocation $location, APIContent $content, $languageCode)
    {
        return new Node(
            [
                'contentInfo' => $this->mapContentInfo($content->versionInfo, $languageCode),
                'innerLocation' => $location,
                'content' => $this->mapContent($content, $languageCode),
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository Field to the Site Field.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType $contentType
     *
     * @return mixed|\Netgen\EzPlatformSiteApi\API\Values\Field
     */
    private function mapFieldData(APIField $field, ContentType $contentType)
    {
        $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);
        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $isEmpty = $this->fieldTypeService->getFieldType($fieldTypeIdentifier)->isEmptyValue(
            $field->value
        );

        return [
            'isEmpty' => $isEmpty,
            'innerField' => $field,
        ];
    }
}
