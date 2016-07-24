<?php

namespace Netgen\EzPlatformSite\Core\Persistence\InMemory;

use Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\MemoryCachingHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct as GroupUpdateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandlerInterface;
use eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct;
use RuntimeException;

/**
 * @internal
 * In-memory caching implementation of ContentType handler.
 */
class ContentTypeHandler extends MemoryCachingHandler implements ContentTypeHandlerInterface, IdentifierMapProvider
{
    /**
     * Inner handler to dispatch calls to.
     *
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    protected $innerHandler;

    /**
     * Local in-memory cache for groups in one single request.
     *
     * @var array
     */
    protected $groups;

    /**
     * Local in-memory cache for content types in one single request.
     *
     * @var array
     */
    protected $contentTypes;

    /**
     * Local in-memory cache for field definitions in one single request.
     *
     * @var array
     */
    protected $fieldDefinitions;

    /**
     * Local in-memory cache for searchable field map in one single request.
     *
     * @var array
     */
    protected $searchableFieldMap = null;

    /**
     * Local in-memory cache for identifier map in one single request.
     *
     * @var array
     */
    private $identifierMap = null;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $handler
     */
    public function __construct(ContentTypeHandlerInterface $handler)
    {
        $this->innerHandler = $handler;
    }

    public function createGroup(GroupCreateStruct $createStruct)
    {
        $this->clearCache();

        return $this->innerHandler->createGroup($createStruct);
    }

    public function updateGroup(GroupUpdateStruct $struct)
    {
        $this->clearCache();

        return $this->innerHandler->updateGroup($struct);
    }

    public function deleteGroup($groupId)
    {
        $this->clearCache();

        return $this->innerHandler->deleteGroup($groupId);
    }

    public function loadGroup($groupId)
    {
        if (isset($this->groups[$groupId])) {
            return $this->groups[$groupId];
        }

        return $this->groups[$groupId] = $this->innerHandler->loadGroup($groupId);
    }

    public function loadGroupByIdentifier($identifier)
    {
        if (isset($this->groups[$identifier])) {
            return $this->groups[$identifier];
        }

        return $this->groups[$identifier] = $this->innerHandler->loadGroupByIdentifier($identifier);
    }

    public function loadAllGroups()
    {
        return $this->innerHandler->loadAllGroups();
    }

    public function loadContentTypes($groupId, $status = 0)
    {
        return $this->innerHandler->loadContentTypes($groupId, $status);
    }

    public function load($contentTypeId, $status = Type::STATUS_DEFINED)
    {
        if (isset($this->contentTypes['id'][$contentTypeId][$status])) {
            return $this->contentTypes['id'][$contentTypeId][$status];
        }

        return $this->contentTypes['id'][$contentTypeId][$status] =
            $this->innerHandler->load($contentTypeId, $status);
    }

    public function loadByIdentifier($identifier)
    {
        if (isset($this->contentTypes['identifier'][$identifier])) {
            return $this->contentTypes['identifier'][$identifier];
        }

        return $this->contentTypes['identifier'][$identifier] =
            $this->innerHandler->loadByIdentifier($identifier);
    }

    public function loadByRemoteId($remoteId)
    {
        if (isset($this->contentTypes['remoteId'][$remoteId])) {
            return $this->contentTypes['remoteId'][$remoteId];
        }

        return $this->contentTypes['remoteId'][$remoteId] =
            $this->innerHandler->loadByRemoteId($remoteId);
    }

    public function create(CreateStruct $createStruct)
    {
        $this->clearCache();

        return $this->innerHandler->create($createStruct);
    }

    public function update($typeId, $status, UpdateStruct $contentType)
    {
        $this->clearCache();

        return $this->innerHandler->update($typeId, $status, $contentType);
    }

    public function delete($contentTypeId, $status)
    {
        $this->clearCache();

        return $this->innerHandler->delete($contentTypeId, $status);
    }

    public function createDraft($modifierId, $contentTypeId)
    {
        $this->clearCache();

        return $this->innerHandler->createDraft($modifierId, $contentTypeId);
    }

    public function copy($userId, $contentTypeId, $status)
    {
        $this->clearCache();

        return $this->innerHandler->copy($userId, $contentTypeId, $status);
    }

    public function unlink($groupId, $contentTypeId, $status)
    {
        $this->clearCache();

        return $this->innerHandler->unlink($groupId, $contentTypeId, $status);
    }

    public function link($groupId, $contentTypeId, $status)
    {
        $this->clearCache();

        return $this->innerHandler->link($groupId, $contentTypeId, $status);
    }

    public function getFieldDefinition($id, $status)
    {
        if (isset($this->fieldDefinitions[$id][$status])) {
            return $this->fieldDefinitions[$id][$status];
        }

        return $this->fieldDefinitions[$id][$status] =
            $this->innerHandler->getFieldDefinition($id, $status);
    }

    public function getContentCount($contentTypeId)
    {
        return $this->innerHandler->getContentCount($contentTypeId);
    }

    public function addFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition)
    {
        $this->clearCache();

        return $this->innerHandler->addFieldDefinition($contentTypeId, $status, $fieldDefinition);
    }

    public function removeFieldDefinition($contentTypeId, $status, $fieldDefinitionId)
    {
        $this->clearCache();

        return $this->innerHandler->removeFieldDefinition($contentTypeId, $status, $fieldDefinitionId);
    }

    public function updateFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition)
    {
        $this->clearCache();

        return $this->innerHandler->updateFieldDefinition($contentTypeId, $status, $fieldDefinition);
    }

    public function publish($contentTypeId)
    {
        $this->clearCache();

        return $this->innerHandler->publish($contentTypeId);
    }

    public function getSearchableFieldMap()
    {
        if ($this->searchableFieldMap !== null) {
            return $this->searchableFieldMap;
        }

        return $this->searchableFieldMap = $this->innerHandler->getSearchableFieldMap();
    }

    public function getIdentifierMap()
    {
        if (!$this->innerHandler instanceof IdentifierMapProvider) {
            throw new RuntimeException('Expected IdentifierMapProvider implementation');
        }

        if ($this->identifierMap !== null) {
            return $this->identifierMap;
        }

        return $this->identifierMap = $this->innerHandler->getIdentifierMap();
    }

    /**
     * Clear internal caches.
     */
    public function clearCache()
    {
        $this->groups = $this->contentTypes = $this->fieldDefinitions = [];
        $this->searchableFieldMap = $this->identifierMap = null;
    }
}
