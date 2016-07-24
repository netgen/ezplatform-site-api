<?php

namespace Netgen\EzPlatformSite\Core\Site;

use Netgen\EzPlatformSite\API\Values\ContentInfo;
use Netgen\EzPlatformSite\API\Values\Field;
use Netgen\EzPlatformSite\API\Values\Location;
use Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider;
use Netgen\EzPlatformSite\Core\Site\Values\Content;
use Netgen\EzPlatformSite\Core\Site\Values\Node;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Field as APIField;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use RuntimeException;

/**
 * @internal
 *
 * Domain object mapper is an internal service that maps eZ Platform Repository objects
 * to the native domain objects.
 */
final class DomainObjectMapper
{
    /**
     * @var \Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider
     */
    private $identifierMapProvider;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @param \Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider $identifierMapProvider
     * @param \eZ\Publish\API\Repository\FieldTypeService
     */
    public function __construct(
        IdentifierMapProvider $identifierMapProvider,
        FieldTypeService $fieldTypeService
    ) {
        $this->identifierMapProvider = $identifierMapProvider;
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * Maps Repository Content to the Site Content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSite\Core\Site\Values\Content
     */
    public function mapContent(APIContent $content, $languageCode)
    {
        $fields = $content->getFieldsByLanguage($languageCode);
        $fieldsById = [];
        $fieldsByIdentifier = [];
        foreach ($fields as $field) {
            $siteField = $this->mapField(
                $field,
                $content->versionInfo->contentInfo->contentTypeId
            );
            $fieldsById[$field->id] = $siteField;
            $fieldsByIdentifier[$field->fieldDefIdentifier] = $siteField;
        }

        return new Content(
            [
                'fields' => $fieldsByIdentifier,
                'fieldsById' => $fieldsById,
                'contentInfo' => $this->mapContentInfo($content->versionInfo, $languageCode),
                'innerContent' => $content,
            ]
        );
    }

    /**
     * Maps Repository ContentInfo to the Site ContentInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSite\API\Values\ContentInfo
     */
    public function mapContentInfo(VersionInfo $versionInfo, $languageCode)
    {
        $contentInfo = $versionInfo->contentInfo;

        return new ContentInfo(
            [
                'id' => $contentInfo->id,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $versionInfo->getName($languageCode),
                'contentTypeIdentifier' => $this->getContentTypeIdentifier(
                    $contentInfo->contentTypeId
                ),
                'languageCode' => $languageCode,
                'innerContentInfo' => $versionInfo->contentInfo,
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
     * @return \Netgen\EzPlatformSite\API\Values\Location
     */
    public function mapLocation(APILocation $location, VersionInfo $versionInfo, $languageCode)
    {
        return new Location(
            [
                'id' => $location->id,
                'contentInfo' => $this->mapContentInfo($versionInfo, $languageCode),
                'innerLocation' => $location,
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
     * @return \Netgen\EzPlatformSite\Core\Site\Values\Node
     */
    public function mapNode(APILocation $location, APIContent $content, $languageCode)
    {
        $fields = $content->getFieldsByLanguage($languageCode);
        $fieldsById = [];
        $fieldsByIdentifier = [];
        foreach ($fields as $field) {
            $siteField = $this->mapField(
                $field,
                $content->versionInfo->contentInfo->contentTypeId
            );
            $fieldsById[$field->id] = $siteField;
            $fieldsByIdentifier[$field->fieldDefIdentifier] = $siteField;
        }

        return new Node(
            [
                'fields' => $fieldsByIdentifier,
                'fieldsById' => $fieldsById,
                'contentInfo' => $this->mapContentInfo($content->versionInfo, $languageCode),
                'location' => $this->mapLocation($location, $content->versionInfo, $languageCode),
                'innerContent' => $content,
            ]
        );
    }

    /**
     * Maps Repository Field to the Site Field.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @param string|int $contentTypeId
     *
     * @return \Netgen\EzPlatformSite\API\Values\Field
     */
    private function mapField(APIField $field, $contentTypeId)
    {
        $fieldTypeIdentifier = $this->getFieldTypeIdentifier(
            $contentTypeId,
            $field->fieldDefIdentifier
        );
        $isEmpty = $this->fieldTypeService->getFieldType($fieldTypeIdentifier)->isEmptyValue(
            $field->value
        );

        return new Field(
            [
                'id' => $field->id,
                'identifier' => $field->fieldDefIdentifier,
                'typeIdentifier' => $fieldTypeIdentifier,
                'isEmpty' => $isEmpty,
                'value' => $field->value,
                'innerField' => $field,
            ]
        );
    }

    /**
     * Returns ContentType identifier for the given $contentTypeId.
     *
     * @param string|int $contentTypeId
     *
     * @return string
     */
    private function getContentTypeIdentifier($contentTypeId)
    {
        $map = $this->identifierMapProvider->getIdentifierMap();

        if (isset($map[$contentTypeId]['identifier'])) {
            return $map[$contentTypeId]['identifier'];
        }

        throw new RuntimeException(
            "Could not find ContentType identifier for ID '{$contentTypeId}'"
        );
    }

    /**
     * Returns field type identifier for the given parameters.
     *
     * @param string|int $contentTypeId
     * @param string $fieldDefinitionIdentifier
     *
     * @return string
     */
    private function getFieldTypeIdentifier($contentTypeId, $fieldDefinitionIdentifier)
    {
        $map = $this->identifierMapProvider->getIdentifierMap();

        if (isset($map[$contentTypeId]['fieldDefinitions'][$fieldDefinitionIdentifier]['typeIdentifier'])) {
            return $map[$contentTypeId]['fieldDefinitions'][$fieldDefinitionIdentifier]['typeIdentifier'];
        }

        throw new RuntimeException(
            "Could not find FieldType identifier for ContentType ID '{$contentTypeId}' and " .
            "FieldDefinition identifier '{$fieldDefinitionIdentifier}'"
        );
    }
}
