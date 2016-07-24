<?php

namespace Netgen\EzPlatformSite\Core\Persistence\Cache;

use Netgen\EzPlatformSite\Core\Persistence\IdentifierMapProvider;
use eZ\Publish\Core\Persistence\Cache\ContentTypeHandler as PlatformContentTypeHandler;
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
 */
final class ContentTypeHandler extends PlatformContentTypeHandler implements ContentTypeHandlerInterface, IdentifierMapProvider
{
    public function createGroup(GroupCreateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, array('struct' => $struct));
        $group = $this->persistenceHandler->contentTypeHandler()->createGroup($struct);

        $this->cache->getItem('contentTypeGroup', $group->id)->set($group);

        return $group;
    }

    public function updateGroup(GroupUpdateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, array('struct' => $struct));

        $this->cache
            ->getItem('contentTypeGroup', $struct->id)
            ->set($group = $this->persistenceHandler->contentTypeHandler()->updateGroup($struct));

        return $group;
    }

    public function deleteGroup($groupId)
    {
        $this->logger->logCall(__METHOD__, array('group' => $groupId));
        $return = $this->persistenceHandler->contentTypeHandler()->deleteGroup($groupId);

        $this->cache->clear('contentTypeGroup', $groupId);

        return $return;
    }

    public function loadGroup($groupId)
    {
        $cache = $this->cache->getItem('contentTypeGroup', $groupId);
        $group = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, array('group' => $groupId));
            $cache->set($group = $this->persistenceHandler->contentTypeHandler()->loadGroup($groupId));
        }

        return $group;
    }

    public function loadGroupByIdentifier($identifier)
    {
        $this->logger->logCall(__METHOD__, array('group' => $identifier));

        return $this->persistenceHandler->contentTypeHandler()->loadGroupByIdentifier($identifier);
    }

    public function loadAllGroups()
    {
        $this->logger->logCall(__METHOD__);

        return $this->persistenceHandler->contentTypeHandler()->loadAllGroups();
    }

    public function loadContentTypes($groupId, $status = Type::STATUS_DEFINED)
    {
        $this->logger->logCall(__METHOD__, array('group' => $groupId, 'status' => $status));

        return $this->persistenceHandler->contentTypeHandler()->loadContentTypes($groupId, $status);
    }

    public function load($typeId, $status = Type::STATUS_DEFINED)
    {
        if ($status !== Type::STATUS_DEFINED) {
            $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status));

            return $this->persistenceHandler->contentTypeHandler()->load($typeId, $status);
        }

        // Get cache for published content types
        $cache = $this->cache->getItem('contentType', $typeId);
        $type = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status));
            $cache->set($type = $this->persistenceHandler->contentTypeHandler()->load($typeId, $status));
        }

        return $type;
    }

    public function loadByIdentifier($identifier)
    {
        // Get identifier to id cache if there is one (avoids caching an object several times)
        $cache = $this->cache->getItem('contentType', 'identifier', $identifier);
        $typeId = $cache->get();
        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__, array('type' => $identifier));
            $type = $this->persistenceHandler->contentTypeHandler()->loadByIdentifier($identifier);
            $cache->set($type->id);
            // Warm contentType cache in case it's not set
            $this->cache->getItem('contentType', $type->id)->set($type);
        } else {
            // Reuse load() if we have id (it should be cached anyway)
            $type = $this->load($typeId);
        }

        return $type;
    }

    public function loadByRemoteId($remoteId)
    {
        $this->logger->logCall(__METHOD__, array('type' => $remoteId));

        return $this->persistenceHandler->contentTypeHandler()->loadByRemoteId($remoteId);
    }

    public function create(CreateStruct $contentType)
    {
        $this->logger->logCall(__METHOD__, array('struct' => $contentType));
        $type = $this->persistenceHandler->contentTypeHandler()->create($contentType);

        if ($type->status === Type::STATUS_DEFINED) {
            // Warm cache
            $this->cache->getItem('contentType', $type->id)->set($type);
            $this->cache->getItem('contentType', 'identifier', $type->identifier)->set($type->id);
            $this->cache->clear('searchableFieldMap');
            $this->cache->clear('identifierMap');
        }

        return $type;
    }

    public function update($typeId, $status, UpdateStruct $struct)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status, 'struct' => $struct));
        if ($status !== Type::STATUS_DEFINED) {
            return $this->persistenceHandler->contentTypeHandler()->update($typeId, $status, $struct);
        }

        // Warm cache
        $this->cache
            ->getItem('contentType', $typeId)
            ->set($type = $this->persistenceHandler->contentTypeHandler()->update($typeId, $status, $struct));

        // Clear identifier cache in case it was changed before warming the new one
        $this->cache->clear('contentType', 'identifier');
        $this->cache->clear('searchableFieldMap');
        $this->cache->clear('identifierMap');
        $this->cache->getItem('contentType', 'identifier', $type->identifier)->set($typeId);

        return $type;
    }

    public function delete($typeId, $status)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status));
        $return = $this->persistenceHandler->contentTypeHandler()->delete($typeId, $status);

        if ($status === Type::STATUS_DEFINED) {
            // Clear type cache and all identifier cache (as we don't know the identifier)
            $this->cache->clear('contentType', $typeId);
            $this->cache->clear('contentType', 'identifier');
            $this->cache->clear('searchableFieldMap');
            $this->cache->clear('identifierMap');
        }

        return $return;
    }

    public function createDraft($modifierId, $typeId)
    {
        $this->logger->logCall(__METHOD__, array('modifier' => $modifierId, 'type' => $typeId));

        return $this->persistenceHandler->contentTypeHandler()->createDraft($modifierId, $typeId);
    }

    public function copy($userId, $typeId, $status)
    {
        $this->logger->logCall(__METHOD__, array('user' => $userId, 'type' => $typeId, 'status' => $status));

        return $this->persistenceHandler->contentTypeHandler()->copy($userId, $typeId, $status);
    }

    public function unlink($groupId, $typeId, $status)
    {
        $this->logger->logCall(__METHOD__, array('group' => $groupId, 'type' => $typeId, 'status' => $status));
        $return = $this->persistenceHandler->contentTypeHandler()->unlink($groupId, $typeId, $status);

        if ($status === Type::STATUS_DEFINED) {
            $this->cache->clear('contentType', $typeId);
        }

        return $return;
    }

    public function link($groupId, $typeId, $status)
    {
        $this->logger->logCall(__METHOD__, array('group' => $groupId, 'type' => $typeId, 'status' => $status));
        $return = $this->persistenceHandler->contentTypeHandler()->link($groupId, $typeId, $status);

        if ($status === Type::STATUS_DEFINED) {
            $this->cache->clear('contentType', $typeId);
        }

        return $return;
    }

    public function getFieldDefinition($id, $status)
    {
        $this->logger->logCall(__METHOD__, array('field' => $id, 'status' => $status));

        return $this->persistenceHandler->contentTypeHandler()->getFieldDefinition($id, $status);
    }

    public function getContentCount($contentTypeId)
    {
        $this->logger->logCall(__METHOD__, array('contentTypeId' => $contentTypeId));

        return $this->persistenceHandler->contentTypeHandler()->getContentCount($contentTypeId);
    }

    public function addFieldDefinition($typeId, $status, FieldDefinition $struct)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status, 'struct' => $struct));
        $return = $this->persistenceHandler->contentTypeHandler()->addFieldDefinition(
            $typeId,
            $status,
            $struct
        );

        if ($status === Type::STATUS_DEFINED) {
            $this->cache->clear('contentType', $typeId);
            $this->cache->clear('searchableFieldMap');
            $this->cache->clear('identifierMap');
        }

        return $return;
    }

    public function removeFieldDefinition($typeId, $status, $fieldDefinitionId)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status, 'field' => $fieldDefinitionId));
        $this->persistenceHandler->contentTypeHandler()->removeFieldDefinition(
            $typeId,
            $status,
            $fieldDefinitionId
        );

        if ($status === Type::STATUS_DEFINED) {
            $this->cache->clear('contentType', $typeId);
            $this->cache->clear('searchableFieldMap');
            $this->cache->clear('identifierMap');
        }
    }

    public function updateFieldDefinition($typeId, $status, FieldDefinition $struct)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId, 'status' => $status, 'struct' => $struct));
        $this->persistenceHandler->contentTypeHandler()->updateFieldDefinition(
            $typeId,
            $status,
            $struct
        );

        if ($status === Type::STATUS_DEFINED) {
            $this->cache->clear('contentType', $typeId);
            $this->cache->clear('searchableFieldMap');
            $this->cache->clear('identifierMap');
        }
    }

    public function publish($typeId)
    {
        $this->logger->logCall(__METHOD__, array('type' => $typeId));
        $this->persistenceHandler->contentTypeHandler()->publish($typeId);

        // Clear type cache and all identifier cache (as we don't know the identifier)
        $this->cache->clear('contentType', $typeId);
        $this->cache->clear('contentType', 'identifier');
        $this->cache->clear('searchableFieldMap');
        $this->cache->clear('identifierMap');

        // clear content cache
        $this->cache->clear('content');//TIMBER! (possible content changes)
    }

    public function getSearchableFieldMap()
    {
        $cache = $this->cache->getItem('searchableFieldMap');

        $fieldMap = $cache->get();

        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__);
            $fieldMap = $this->persistenceHandler->contentTypeHandler()->getSearchableFieldMap();
            $cache->set($fieldMap);
        }

        return $fieldMap;
    }

    public function getIdentifierMap()
    {
        $contentTypeHandler = $this->persistenceHandler->contentTypeHandler();
        if (!$contentTypeHandler instanceof IdentifierMapProvider) {
            throw new RuntimeException('Expected IdentifierMapProvider implementation');
        }

        $cache = $this->cache->getItem('identifierMap');

        $identifierMap = $cache->get();

        if ($cache->isMiss()) {
            $this->logger->logCall(__METHOD__);
            $identifierMap = $contentTypeHandler->getIdentifierMap();
            $cache->set($identifierMap);
        }

        return $identifierMap;
    }
}
