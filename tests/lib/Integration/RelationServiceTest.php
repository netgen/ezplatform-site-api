<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Netgen\EzPlatformSiteApi\API\Values\Content;

/**
 * Test case for the RelationService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\RelationService
 *
 * @group integration
 * @group relation
 */
class RelationServiceTest extends BaseTest
{
    public function testPrepareTestContent()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        $contentService = $repository->getContentService();
        $languageCode = 'eng-GB';
        $relationId = 57;
        $relationIds = [57, 58];
        $fieldDefinitionIdentifier = 'relation';

        $contentTypeGroup = $contentTypeService->loadContentTypeGroup(1);

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('test_relation');
        $contentTypeCreateStruct->mainLanguageCode = $languageCode;
        $contentTypeCreateStruct->names = [$languageCode => 'Test Relation'];
        $contentTypeCreateStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct([
                'identifier' => $fieldDefinitionIdentifier,
                'fieldTypeIdentifier' => 'ezobjectrelation',
            ])
        );
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentTypeDraft, $languageCode);
        $contentCreateStruct->setField($fieldDefinitionIdentifier, $relationId);
        $contentDraft = $contentService->createContent($contentCreateStruct);
        $relationContent = $contentService->publishVersion($contentDraft->versionInfo);

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('test_relation_list');
        $contentTypeCreateStruct->mainLanguageCode = $languageCode;
        $contentTypeCreateStruct->names = [$languageCode => 'Test RelationList'];
        $contentTypeCreateStruct->addFieldDefinition(
            new FieldDefinitionCreateStruct([
                'identifier' => $fieldDefinitionIdentifier,
                'fieldTypeIdentifier' => 'ezobjectrelationlist',
            ])
        );
        $contentTypeDraft = $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeGroup]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentCreateStruct = $contentService->newContentCreateStruct($contentTypeDraft, $languageCode);
        $contentCreateStruct->setField($fieldDefinitionIdentifier, $relationIds);
        $contentDraft = $contentService->createContent($contentCreateStruct);
        $relationListContent = $contentService->publishVersion($contentDraft->versionInfo);

        return [$fieldDefinitionIdentifier, $relationContent, $relationId, $relationListContent, $relationIds];
    }

    /**
     * @depends testPrepareTestContent
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content[] $testContentItems
     */
    public function testLoadFieldRelation(array $testContentItems)
    {
        list($identifier, $testApiContent, $testRelationId) = $testContentItems;

        $relationService = $this->getSite()->getRelationService();
        $content = $relationService->loadFieldRelation($testApiContent->id, $identifier);

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals($testRelationId, $content->id);
    }

    /**
     * @depends testPrepareTestContent
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content[] $testContentItems
     */
    public function testLoadFieldRelations(array $testContentItems)
    {
        list($identifier, , , $testApiContent, $testRelationIds) = $testContentItems;

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, $identifier);

        $this->assertEquals(count($testRelationIds), count($contentItems));

        foreach ($testRelationIds as $index => $relationId) {
            $content = $contentItems[$index];
            $this->assertInstanceOf(Content::class, $content);
            $this->assertEquals($relationId, $content->id);
        }
    }
}
