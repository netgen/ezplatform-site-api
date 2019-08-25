<?php

declare(strict_types=1);

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
 *
 * @internal
 */
final class RelationServiceTest extends BaseTest
{
    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelation(): void
    {
        [$identifier, $testApiContent, $testRelationId] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $content = $relationService->loadFieldRelation($testApiContent->id, $identifier);

        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals($testRelationId, $content->id);
    }

    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelations(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, $identifier);

        $this->assertEquals(\count($testRelationIds), \count($contentItems));

        foreach ($testRelationIds as $index => $relationId) {
            $content = $contentItems[$index];
            $this->assertInstanceOf(Content::class, $content);
            $this->assertEquals($relationId, $content->id);
        }
    }

    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelationsWithTypeFilter(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, $identifier, ['landing_page']);

        $this->assertEquals(1, \count($contentItems));

        $this->assertInstanceOf(Content::class, $contentItems[0]);
        $this->assertEquals($testRelationIds[0], $contentItems[0]->id);
    }

    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelationsWithLimit(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, $identifier, [], 1);

        $this->assertEquals(1, \count($contentItems));

        $this->assertInstanceOf(Content::class, $contentItems[0]);
        $this->assertEquals($testRelationIds[0], $contentItems[0]->id);
    }

    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelationsWithTypeFilterAndLimit(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, $identifier, ['feedback_form'], 1);

        $this->assertEquals(1, \count($contentItems));

        $this->assertInstanceOf(Content::class, $contentItems[0]);
        $this->assertEquals($testRelationIds[1], $contentItems[0]->id);
    }

    /**
     * @throws \ErrorException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testLoadFieldRelationsForNonexistentField(): void
    {
        [, , , $testApiContent] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $contentItems = $relationService->loadFieldRelations($testApiContent->id, 'nonexistent');

        $this->assertCount(0, $contentItems);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return array
     */
    protected function prepareTestContent(): array
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
}
