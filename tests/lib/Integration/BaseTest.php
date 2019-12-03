<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException;
use eZ\Publish\API\Repository\Tests\BaseTest as APIBaseTest;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field as APIField;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo as APIContentInfo;
use Netgen\EzPlatformSiteApi\API\Values\Field;
use Netgen\EzPlatformSiteApi\API\Values\Fields;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field\SurrogateValue;
use ReflectionProperty;

/**
 * Base class for API integration tests.
 *
 * @internal
 */
abstract class BaseTest extends APIBaseTest
{
    public function getData(string $languageCode): array
    {
        return [
            'name' => $languageCode,
            'contentId' => 61,
            'contentRemoteId' => 'content-remote-id',
            'locationId' => 63,
            'locationRemoteId' => 'location-remote-id',
            'parentLocationId' => 62,
            'locationPriority' => 1,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationPathString' => '/1/2/62/63/',
            'locationPath' => [1, 2, 62, 63],
            'locationDepth' => 3,
            'locationSortField' => APILocation::SORT_FIELD_NODE_ID,
            'locationSortOrder' => APILocation::SORT_ORDER_DESC,
            'contentTypeIdentifier' => 'test-type',
            'contentTypeId' => 37,
            'sectionId' => 1,
            'currentVersionNo' => 1,
            'published' => true,
            'ownerId' => 14,
            'modificationDateTimestamp' => 100,
            'publishedDateTimestamp' => 101,
            'alwaysAvailable' => true,
            'mainLanguageCode' => 'eng-GB',
            'mainLocationId' => 63,
            'contentTypeName' => 'Test type',
            'contentTypeDescription' => 'A test type',
            'languageCode' => $languageCode,
            'fields' => [
                'title' => [
                    'name' => 'Title',
                    'description' => 'Title of the test type',
                    'fieldTypeIdentifier' => 'ezstring',
                    'value' => $languageCode,
                    'isEmpty' => false,
                    'isSurrogate' => false,
                ],
            ],
            'siblingLocationId' => 64,
            'childLocationId' => 65,
        ];
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    protected function overrideSettings(string $name, $value): void
    {
        $settings = $this->getSite()->getSettings();

        $property = new ReflectionProperty(\get_class($settings), $name);
        $property->setAccessible(true);
        $property->setValue($settings, $value);
    }

    /**
     * @throws \ErrorException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSite(): Site
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
        $site = $this->getSetupFactory()->getServiceContainer()->get('netgen.ezplatform_site.site');

        return $site;
    }

    protected function assertContent(Content $content, array $data): void
    {
        [$name, $contentId, , $locationId] = \array_values($data);

        $this->assertSame($contentId, $content->id);
        $this->assertSame($locationId, $content->mainLocationId);
        $this->assertSame($name, $content->name);
        $this->assertEquals($data['mainLocationId'], $content->mainLocationId);
        $this->assertEquals($data['languageCode'], $content->languageCode);
        $this->assertContentInfo($content->contentInfo, $data);
        $this->assertFields($content, $data);
        $this->assertInstanceOf(Content::class, $content->owner);
        $this->assertInstanceOf(Location::class, $content->mainLocation);
        $this->assertInstanceOf(Content::class, $content->owner);
        $this->assertInstanceOf(User::class, $content->innerOwnerUser);
        $this->assertInstanceOf(User::class, $content->innerOwnerUser);
        $this->assertInstanceOf(APIContent::class, $content->innerContent);
        $this->assertInstanceOf(VersionInfo::class, $content->versionInfo);
        $this->assertInstanceOf(VersionInfo::class, $content->innerVersionInfo);

        $locations = $content->getLocations();
        $this->assertIsArray($locations);
        $this->assertCount(1, $locations);
        $this->assertInstanceOf(Location::class, \reset($locations));

        $this->assertTrue(isset($content->id));
        $this->assertTrue(isset($content->name));
        $this->assertTrue(isset($content->mainLocationId));
        $this->assertTrue(isset($content->contentInfo));
        $this->assertFalse(isset($content->nonExistentProperty));

        try {
            $content->nonExistentProperty;
            $this->fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'id' => $content->id,
                'mainLocationId' => $content->mainLocationId,
                'name' => $content->name,
                'languageCode' => $content->languageCode,
                'contentInfo' => $content->contentInfo,
                'fields' => $content->fields,
                'mainLocation' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
                'innerContent' => '[An instance of eZ\Publish\API\Repository\Values\Content\Content]',
                'innerVersionInfo' => '[An instance of eZ\Publish\API\Repository\Values\Content\VersionInfo]',
            ],
            $content->__debugInfo()
        );
    }

    protected function assertContentInfo(APIContentInfo $contentInfo, array $data): void
    {
        [$name, $contentId, $contentRemoteId, $locationId] = \array_values($data);

        $this->assertEquals($contentId, $contentInfo->id);
        $this->assertEquals($contentRemoteId, $contentInfo->remoteId);
        $this->assertEquals($locationId, $contentInfo->mainLocationId);
        $this->assertEquals($name, $contentInfo->name);
        $this->assertEquals($data['contentTypeIdentifier'], $contentInfo->contentTypeIdentifier);
        $this->assertEquals($data['contentTypeId'], $contentInfo->contentTypeId);
        $this->assertEquals($data['sectionId'], $contentInfo->sectionId);
        $this->assertEquals($data['currentVersionNo'], $contentInfo->currentVersionNo);
        $this->assertEquals($data['published'], $contentInfo->published);
        $this->assertEquals($data['ownerId'], $contentInfo->ownerId);
        $this->assertEquals($data['modificationDateTimestamp'], $contentInfo->modificationDate->getTimestamp());
        $this->assertEquals($data['publishedDateTimestamp'], $contentInfo->publishedDate->getTimestamp());
        $this->assertEquals($data['alwaysAvailable'], $contentInfo->alwaysAvailable);
        $this->assertEquals($data['mainLanguageCode'], $contentInfo->mainLanguageCode);
        $this->assertEquals($data['mainLocationId'], $contentInfo->mainLocationId);
        $this->assertEquals($data['contentTypeName'], $contentInfo->contentTypeName);
        $this->assertEquals($data['contentTypeDescription'], $contentInfo->contentTypeDescription);
        $this->assertEquals($data['languageCode'], $contentInfo->languageCode);
        $this->assertInstanceOf(Location::class, $contentInfo->mainLocation);
        $this->assertInstanceOf(ContentInfo::class, $contentInfo->innerContentInfo);
        $this->assertInstanceOf(ContentType::class, $contentInfo->innerContentType);

        $this->assertTrue(isset($contentInfo->name));
        $this->assertTrue(isset($contentInfo->contentTypeIdentifier));
        $this->assertTrue(isset($contentInfo->contentTypeName));
        $this->assertTrue(isset($contentInfo->contentTypeDescription));
        $this->assertTrue(isset($contentInfo->mainLocation));
        $this->assertFalse(isset($contentInfo->nonExistentProperty));

        try {
            $contentInfo->nonExistentProperty;
            $this->fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'id' => $contentInfo->id,
                'contentTypeId' => $contentInfo->contentTypeId,
                'sectionId' => $contentInfo->sectionId,
                'currentVersionNo' => $contentInfo->currentVersionNo,
                'published' => $contentInfo->published,
                'ownerId' => $contentInfo->ownerId,
                'modificationDate' => $contentInfo->modificationDate,
                'publishedDate' => $contentInfo->publishedDate,
                'alwaysAvailable' => $contentInfo->alwaysAvailable,
                'remoteId' => $contentInfo->remoteId,
                'mainLanguageCode' => $contentInfo->mainLanguageCode,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $contentInfo->name,
                'languageCode' => $contentInfo->languageCode,
                'contentTypeIdentifier' => $contentInfo->contentTypeIdentifier,
                'contentTypeName' => $contentInfo->contentTypeName,
                'contentTypeDescription' => $contentInfo->contentTypeDescription,
                'innerContentInfo' => '[An instance of eZ\Publish\API\Repository\Values\Content\ContentInfo]',
                'innerContentType' => '[An instance of eZ\Publish\API\Repository\Values\ContentType\ContentType]',
                'mainLocation' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
            ],
            $contentInfo->__debugInfo()
        );
    }

    protected function assertFields(Content $content, array $data): void
    {
        $this->assertInstanceOf(Fields::class, $content->fields);
        $this->assertCount(\count($data['fields']), $content->fields);

        foreach ($content->fields as $identifier => $field) {
            $this->assertInstanceOf(Field::class, $field);
            $this->assertInstanceOf(Value::class, $field->value);
            $this->assertInstanceOf(APIField::class, $field->innerField);

            $fieldById = $content->getFieldById($field->id);
            $fieldByIdentifier = $content->getField($identifier);
            $fieldByFirstNonEmptyField = $content->getFirstNonEmptyField($identifier);

            $this->assertSame($field, $fieldById);
            $this->assertSame($field, $fieldByIdentifier);
            $this->assertSame($field, $fieldByFirstNonEmptyField);

            $fieldValueById = $content->getFieldValueById($field->id);
            $fieldValueByIdentifier = $content->getFieldValue($identifier);

            $this->assertSame($field->value, $fieldValueById);
            $this->assertSame($fieldValueById, $fieldValueByIdentifier);

            $this->assertSame($content, $field->content);

            $this->assertSame($data['fields'][$identifier]['value'], (string) $field->value);
        }

        foreach ($data['fields'] as $identifier => $fieldData) {
            $this->assertField($content, $identifier, $data['languageCode'], $fieldData);
        }

        $this->assertInstanceOf(Field::class, $content->getField('non_existent_field'));
        $this->assertInstanceOf(Field::class, $content->getFieldById('non_existent_field'));
        $this->assertInstanceOf(SurrogateValue::class, $content->getFieldValue('non_existent_field'));
        $this->assertInstanceOf(SurrogateValue::class, $content->getFieldValueById('non_existent_field'));
        $this->assertInstanceOf(SurrogateValue::class, $content->getFirstNonEmptyField('non_existent_field')->value);
    }

    protected function assertField(Content $content, string $identifier, string $languageCode, array $data): void
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Field|\Netgen\EzPlatformSiteApi\Core\Site\Values\Field $field */
        $field = $content->getField($identifier);

        $this->assertSame($field->id, $field->innerField->id);
        $this->assertSame($data['isEmpty'], $field->isEmpty());
        $this->assertSame($data['isSurrogate'], $field->isSurrogate());
        $this->assertSame($identifier, $field->fieldDefIdentifier);
        $this->assertSame($data['fieldTypeIdentifier'], $field->fieldTypeIdentifier);
        $this->assertSame($data['name'], $field->name);
        $this->assertSame($data['description'], $field->description);
        $this->assertEquals($languageCode, $field->languageCode);

        $this->assertTrue(isset($field->fieldTypeIdentifier));
        $this->assertTrue(isset($field->innerFieldDefinition));
        $this->assertTrue(isset($field->name));
        $this->assertTrue(isset($field->description));
        $this->assertTrue(isset($field->languageCode));
        $this->assertFalse(isset($field->nonExistantProperty));

        try {
            $field->nonExistentProperty;
            $this->fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'id' => $field->id,
                'fieldDefIdentifier' => $field->fieldDefIdentifier,
                'value' => $field->value,
                'languageCode' => $field->languageCode,
                'fieldTypeIdentifier' => $field->fieldTypeIdentifier,
                'name' => $field->name,
                'description' => $field->description,
                'content' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Content]',
                'contentId' => $field->content->id,
                'isEmpty' => $field->isEmpty(),
                'isSurrogate' => $field->isSurrogate(),
                'innerField' => '[An instance of eZ\Publish\API\Repository\Values\Content\Field]',
                'innerFieldDefinition' => $field->innerFieldDefinition,
            ],
            $field->__debugInfo()
        );
    }

    protected function assertLocation(Location $location, array $data): void
    {
        [, , , $locationId, $locationRemoteId, $parentLocationId] = \array_values($data);

        $this->assertEquals($locationId, $location->id);
        $this->assertEquals($locationRemoteId, $location->remoteId);
        $this->assertEquals($parentLocationId, $location->parentLocationId);
        $this->assertEquals(APILocation::STATUS_PUBLISHED, $location->status);
        $this->assertEquals($data['locationPriority'], $location->priority);
        $this->assertEquals($data['locationHidden'], $location->hidden);
        $this->assertEquals($data['locationInvisible'], $location->invisible);
        $this->assertEquals($data['locationPathString'], $location->pathString);
        $this->assertEquals($data['locationPath'], $location->path);
        $this->assertEquals($data['locationDepth'], $location->depth);
        $this->assertEquals($data['locationSortField'], $location->sortField);
        $this->assertEquals($data['locationSortOrder'], $location->sortOrder);
        $this->assertEquals($location->contentInfo->id, $location->contentId);
        $this->assertContentInfo($location->contentInfo, $data);
        $this->assertInstanceOf(APILocation::class, $location->innerLocation);
        $this->assertInstanceOf(Content::class, $location->content);

        $this->assertInstanceOf(Location::class, $location->parent);
        $this->assertEquals($parentLocationId, $location->parent->id);

        $children = $location->getChildren();
        $this->assertIsArray($children);
        $this->assertCount(1, $children);
        $this->assertInstanceOf(Location::class, $children[0]);
        $this->assertEquals($data['childLocationId'], $children[0]->id);

        $firstChild = $location->getFirstChild();
        $this->assertInstanceOf(Location::class, $firstChild);
        $this->assertEquals($data['childLocationId'], $firstChild->id);

        $siblings = $location->getSiblings();
        $this->assertIsArray($siblings);
        $this->assertCount(1, $siblings);
        $this->assertInstanceOf(Location::class, $siblings[0]);
        $this->assertEquals($data['siblingLocationId'], $siblings[0]->id);

        $this->assertTrue(isset($location->contentId));
        $this->assertTrue(isset($location->contentInfo));
        $this->assertFalse(isset($location->nonExistentProperty));

        try {
            $location->nonExistentProperty;
            $this->fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'id' => $location->id,
                'status' => $location->status,
                'priority' => $location->priority,
                'hidden' => $location->hidden,
                'invisible' => $location->invisible,
                'remoteId' => $location->remoteId,
                'parentLocationId' => $location->parentLocationId,
                'pathString' => $location->pathString,
                'path' => $location->path,
                'depth' => $location->depth,
                'sortField' => $location->sortField,
                'sortOrder' => $location->sortOrder,
                'contentId' => $location->contentId,
                'innerLocation' => '[An instance of eZ\Publish\API\Repository\Values\Content\Location]',
                'contentInfo' => $location->contentInfo,
                'parent' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Location]',
                'content' => '[An instance of Netgen\EzPlatformSiteApi\API\Values\Content]',
            ],
            $location->__debugInfo()
        );
    }
}
