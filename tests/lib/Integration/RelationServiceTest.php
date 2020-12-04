<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;

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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $content = $relationService->loadFieldRelation($testSiteContent, $identifier);

        self::assertInstanceOf(Content::class, $content);
        self::assertEquals($testRelationId, $content->id);
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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $contentItems = $relationService->loadFieldRelations($testSiteContent, $identifier);

        self::assertSameSize($testRelationIds, $contentItems);

        foreach ($testRelationIds as $index => $relationId) {
            $content = $contentItems[$index];
            self::assertInstanceOf(Content::class, $content);
            self::assertEquals($relationId, $content->id);
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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $contentItems = $relationService->loadFieldRelations($testSiteContent, $identifier, ['landing_page']);

        self::assertCount(1, $contentItems);

        self::assertInstanceOf(Content::class, $contentItems[0]);
        self::assertEquals($testRelationIds[0], $contentItems[0]->id);
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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $contentItems = $relationService->loadFieldRelations($testSiteContent, $identifier, [], 1);

        self::assertCount(1, $contentItems);

        self::assertInstanceOf(Content::class, $contentItems[0]);
        self::assertEquals($testRelationIds[0], $contentItems[0]->id);
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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $contentItems = $relationService->loadFieldRelations($testSiteContent, $identifier, ['feedback_form'], 1);

        self::assertCount(1, $contentItems);

        self::assertInstanceOf(Content::class, $contentItems[0]);
        self::assertEquals($testRelationIds[1], $contentItems[0]->id);
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
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $contentItems = $relationService->loadFieldRelations($testSiteContent, 'nonexistent');

        self::assertCount(0, $contentItems);
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelation(): void
    {
        [$identifier, $testApiContent, $testRelationId] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $location = $relationService->loadLocationFieldRelation($testSiteContent, $identifier);

        self::assertInstanceOf(Location::class, $location);
        self::assertEquals($testRelationId, $location->content->id);
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelations(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $locations = $relationService->loadLocationFieldRelations($testSiteContent, $identifier);

        self::assertSameSize($testRelationIds, $locations);

        foreach ($testRelationIds as $index => $relationId) {
            $location = $locations[$index];
            self::assertInstanceOf(Location::class, $location);
            self::assertEquals($relationId, $location->content->id);
        }
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelationsWithTypeFilter(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $locations = $relationService->loadLocationFieldRelations($testSiteContent, $identifier, ['landing_page']);

        self::assertCount(1, $locations);

        self::assertInstanceOf(Location::class, $locations[0]);
        self::assertEquals($testRelationIds[0], $locations[0]->content->id);
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelationsWithLimit(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $locations = $relationService->loadLocationFieldRelations($testSiteContent, $identifier, [], 1);

        self::assertCount(1, $locations);

        self::assertInstanceOf(Location::class, $locations[0]);
        self::assertEquals($testRelationIds[0], $locations[0]->content->id);
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelationsWithTypeFilterAndLimit(): void
    {
        [$identifier, , , $testApiContent, $testRelationIds] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $locations = $relationService->loadLocationFieldRelations($testSiteContent, $identifier, ['feedback_form'], 1);

        self::assertCount(1, $locations);

        self::assertInstanceOf(Location::class, $locations[0]);
        self::assertEquals($testRelationIds[1], $locations[0]->content->id);
    }

    /**
     * @throws \Exception
     */
    public function testLoadLocationFieldRelationsForNonexistentField(): void
    {
        [, , , $testApiContent] = $this->prepareTestContent();

        $relationService = $this->getSite()->getRelationService();
        $loadService = $this->getSite()->getLoadService();
        $testSiteContent = $loadService->loadContent($testApiContent->id);
        $locations = $relationService->loadLocationFieldRelations($testSiteContent, 'nonexistent');

        self::assertCount(0, $locations);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentTypeFieldDefinitionValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
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
