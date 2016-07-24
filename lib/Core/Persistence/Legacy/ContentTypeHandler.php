<?php

namespace Netgen\EzPlatformSite\Core\Persistence\Legacy;

use Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider;
use Netgen\EzPlatformSite\Core\Persistence\Legacy\IdentifierMap\Gateway;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct as GroupUpdateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandlerInterface;
use eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct;
use ArrayObject;

/**
 * @internal
 */
final class ContentTypeHandler implements ContentTypeHandlerInterface, IdentifierMapProvider
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $innerHandler;

    /**
     * @var \Netgen\EzPlatformSite\Core\Persistence\Legacy\IdentifierMap\Gateway
     */
    private $gateway;

    public function __construct(
        ContentTypeHandlerInterface $innerHandler,
        Gateway $gateway
    ) {
        $this->innerHandler = $innerHandler;
        $this->gateway = $gateway;
    }

    public function createGroup(GroupCreateStruct $group)
    {
        return $this->innerHandler->createGroup($group);
    }

    public function updateGroup(GroupUpdateStruct $group)
    {
        return $this->innerHandler->updateGroup($group);
    }

    public function deleteGroup($groupId)
    {
        return $this->innerHandler->deleteGroup($groupId);
    }

    public function loadGroup($groupId)
    {
        return $this->innerHandler->loadGroup($groupId);
    }

    public function loadGroupByIdentifier($identifier)
    {
        return $this->innerHandler->loadGroupByIdentifier($identifier);
    }

    public function loadAllGroups()
    {
        return $this->innerHandler->loadAllGroups();
    }

    public function loadContentTypes($groupId, $status = Type::STATUS_DEFINED)
    {
        return $this->innerHandler->loadContentTypes($groupId, $status);
    }

    public function load($contentTypeId, $status = Type::STATUS_DEFINED)
    {
        return $this->innerHandler->load($contentTypeId, $status);
    }

    public function loadByIdentifier($identifier)
    {
        return $this->innerHandler->loadByIdentifier($identifier);
    }

    public function loadByRemoteId($remoteId)
    {
        return $this->innerHandler->loadByRemoteId($remoteId);
    }

    public function create(CreateStruct $contentType)
    {
        return $this->innerHandler->create($contentType);
    }

    public function update($contentTypeId, $status, UpdateStruct $contentType)
    {
        return $this->innerHandler->update($contentTypeId, $status, $contentType);
    }

    public function delete($contentTypeId, $status)
    {
        return $this->innerHandler->delete($contentTypeId, $status);
    }

    public function createDraft($modifierId, $contentTypeId)
    {
        return $this->innerHandler->createDraft($modifierId, $contentTypeId);
    }

    public function copy($userId, $contentTypeId, $status)
    {
        return $this->innerHandler->copy($userId, $contentTypeId, $status);
    }

    public function unlink($groupId, $contentTypeId, $status)
    {
        return $this->innerHandler->unlink($groupId, $contentTypeId, $status);
    }

    public function link($groupId, $contentTypeId, $status)
    {
        return $this->innerHandler->link($groupId, $contentTypeId, $status);
    }

    public function getFieldDefinition($id, $status)
    {
        return $this->innerHandler->getFieldDefinition($id, $status);
    }

    public function getContentCount($contentTypeId)
    {
        return $this->innerHandler->getContentCount($contentTypeId);
    }

    public function addFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition)
    {
        return $this->innerHandler->addFieldDefinition($contentTypeId, $status, $fieldDefinition);
    }

    public function removeFieldDefinition($contentTypeId, $status, $fieldDefinitionId)
    {
        return $this->innerHandler->removeFieldDefinition(
            $contentTypeId,
            $status,
            $fieldDefinitionId
        );
    }

    public function updateFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition)
    {
        return $this->innerHandler->updateFieldDefinition(
            $contentTypeId,
            $status,
            $fieldDefinition
        );
    }

    public function publish($contentTypeId)
    {
        return $this->innerHandler->publish($contentTypeId);
    }

    public function getSearchableFieldMap()
    {
        return $this->innerHandler->getSearchableFieldMap();
    }

    public function getIdentifierMap()
    {
        $rawData = $this->gateway->getIdentifierMapData();
        $formattedData = new ArrayObject();

        foreach ($rawData as $row) {
            $contentTypeId = $row['content_type_id'];
            $fieldDefinitionIdentifier = $row['field_definition_identifier'];

            if (!isset($formattedData[$contentTypeId])) {
                $formattedData[$contentTypeId] = [
                    'identifier' => $row['content_type_identifier'],
                    'fieldDefinitions' => [],
                ];
            }

            $formattedData[$contentTypeId]['fieldDefinitions'][$fieldDefinitionIdentifier] = [
                'id' => $row['field_definition_id'],
                'typeIdentifier' => $row['field_type_identifier'],
            ];
        }

        return $formattedData;
    }
}
