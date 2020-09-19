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
use function array_values;
use function count;
use function get_class;
use function reset;

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
            'contentIsHidden' => false,
            'contentIsVisible' => true,
            'locationPriority' => 1,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationExplicitlyHidden' => false,
            'locationIsVisible' => true,
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
     * @param mixed $value
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    protected function overrideSettings(string $name, $value): void
    {
        $settings = $this->getSite()->getSettings();

        $property = new ReflectionProperty(get_class($settings), $name);
        $property->setAccessible(true);
        $property->setValue($settings, $value);
    }

    /**
     * @throws \ErrorException
     */
    protected function getSite(): Site
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
        $site = $this->getSetupFactory()->getServiceContainer()->get('netgen.ezplatform_site.site');

        return $site;
    }

    protected function assertContent(Content $content, array $data): void
    {
        [$name, $contentId, , $locationId] = array_values($data);

        self::assertSame($contentId, $content->id);
        self::assertSame($locationId, $content->mainLocationId);
        self::assertSame($name, $content->name);
        self::assertEquals($data['mainLocationId'], $content->mainLocationId);
        self::assertEquals($data['languageCode'], $content->languageCode);
        self::assertEquals($data['contentIsVisible'], $content->isVisible);
        $this->assertContentInfo($content->contentInfo, $data);
        $this->assertFields($content, $data);
        self::assertInstanceOf(Content::class, $content->owner);
        self::assertInstanceOf(Location::class, $content->mainLocation);
        self::assertInstanceOf(Content::class, $content->owner);
        self::assertInstanceOf(User::class, $content->innerOwnerUser);
        self::assertInstanceOf(User::class, $content->innerOwnerUser);
        self::assertInstanceOf(APIContent::class, $content->innerContent);
        self::assertInstanceOf(VersionInfo::class, $content->versionInfo);
        self::assertInstanceOf(VersionInfo::class, $content->innerVersionInfo);

        $locations = $content->getLocations();
        self::assertIsArray($locations);
        self::assertCount(1, $locations);
        self::assertInstanceOf(Location::class, reset($locations));

        self::assertTrue(isset($content->id));
        self::assertTrue(isset($content->name));
        self::assertTrue(isset($content->mainLocationId));
        self::assertTrue(isset($content->contentInfo));
        self::assertFalse(isset($content->nonExistentProperty));

        try {
            $content->nonExistentProperty;
            self::fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        self::assertEquals(
            [
                'id' => $content->id,
                'mainLocationId' => $content->mainLocationId,
                'name' => $content->name,
                'languageCode' => $content->languageCode,
                'isVisible' => $content->isVisible,
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
        [$name, $contentId, $contentRemoteId, $locationId] = array_values($data);

        self::assertEquals($contentId, $contentInfo->id);
        self::assertEquals($contentRemoteId, $contentInfo->remoteId);
        self::assertEquals($locationId, $contentInfo->mainLocationId);
        self::assertEquals($name, $contentInfo->name);
        self::assertEquals($data['contentTypeIdentifier'], $contentInfo->contentTypeIdentifier);
        self::assertEquals($data['contentTypeId'], $contentInfo->contentTypeId);
        self::assertEquals($data['sectionId'], $contentInfo->sectionId);
        self::assertEquals($data['currentVersionNo'], $contentInfo->currentVersionNo);
        self::assertEquals($data['published'], $contentInfo->published);
        self::assertEquals($data['contentIsHidden'], $contentInfo->isHidden);
        self::assertEquals($data['contentIsVisible'], $contentInfo->isVisible);
        self::assertEquals($data['ownerId'], $contentInfo->ownerId);
        self::assertEquals($data['modificationDateTimestamp'], $contentInfo->modificationDate->getTimestamp());
        self::assertEquals($data['publishedDateTimestamp'], $contentInfo->publishedDate->getTimestamp());
        self::assertEquals($data['alwaysAvailable'], $contentInfo->alwaysAvailable);
        self::assertEquals($data['mainLanguageCode'], $contentInfo->mainLanguageCode);
        self::assertEquals($data['mainLocationId'], $contentInfo->mainLocationId);
        self::assertEquals($data['contentTypeName'], $contentInfo->contentTypeName);
        self::assertEquals($data['contentTypeDescription'], $contentInfo->contentTypeDescription);
        self::assertEquals($data['languageCode'], $contentInfo->languageCode);
        self::assertInstanceOf(Location::class, $contentInfo->mainLocation);
        self::assertInstanceOf(ContentInfo::class, $contentInfo->innerContentInfo);
        self::assertInstanceOf(ContentType::class, $contentInfo->innerContentType);

        self::assertTrue(isset($contentInfo->name));
        self::assertTrue(isset($contentInfo->contentTypeIdentifier));
        self::assertTrue(isset($contentInfo->contentTypeName));
        self::assertTrue(isset($contentInfo->contentTypeDescription));
        self::assertTrue(isset($contentInfo->mainLocation));
        self::assertFalse(isset($contentInfo->nonExistentProperty));

        try {
            $contentInfo->nonExistentProperty;
            self::fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        self::assertEquals(
            [
                'id' => $contentInfo->id,
                'contentTypeId' => $contentInfo->contentTypeId,
                'sectionId' => $contentInfo->sectionId,
                'currentVersionNo' => $contentInfo->currentVersionNo,
                'published' => $contentInfo->published,
                'isHidden' => $contentInfo->isHidden,
                'isVisible' => !$contentInfo->isHidden,
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
        self::assertInstanceOf(Fields::class, $content->fields);
        self::assertCount(count($data['fields']), $content->fields);

        foreach ($content->fields as $identifier => $field) {
            self::assertInstanceOf(Field::class, $field);
            self::assertInstanceOf(Value::class, $field->value);
            self::assertInstanceOf(APIField::class, $field->innerField);

            $fieldById = $content->getFieldById($field->id);
            $fieldByIdentifier = $content->getField($identifier);
            $fieldByFirstNonEmptyField = $content->getFirstNonEmptyField($identifier);

            self::assertSame($field, $fieldById);
            self::assertSame($field, $fieldByIdentifier);
            self::assertSame($field, $fieldByFirstNonEmptyField);

            $fieldValueById = $content->getFieldValueById($field->id);
            $fieldValueByIdentifier = $content->getFieldValue($identifier);

            self::assertSame($field->value, $fieldValueById);
            self::assertSame($fieldValueById, $fieldValueByIdentifier);

            self::assertSame($content, $field->content);

            self::assertSame($data['fields'][$identifier]['value'], (string) $field->value);
        }

        foreach ($data['fields'] as $identifier => $fieldData) {
            $this->assertField($content, $identifier, $data['languageCode'], $fieldData);
        }

        self::assertInstanceOf(Field::class, $content->getField('non_existent_field'));
        self::assertInstanceOf(Field::class, $content->getFieldById('non_existent_field'));
        self::assertInstanceOf(SurrogateValue::class, $content->getFieldValue('non_existent_field'));
        self::assertInstanceOf(SurrogateValue::class, $content->getFieldValueById('non_existent_field'));
        self::assertInstanceOf(SurrogateValue::class, $content->getFirstNonEmptyField('non_existent_field')->value);
    }

    protected function assertField(Content $content, string $identifier, string $languageCode, array $data): void
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Field|\Netgen\EzPlatformSiteApi\Core\Site\Values\Field $field */
        $field = $content->getField($identifier);

        self::assertSame($field->id, $field->innerField->id);
        self::assertSame($data['isEmpty'], $field->isEmpty());
        self::assertSame($data['isSurrogate'], $field->isSurrogate());
        self::assertSame($identifier, $field->fieldDefIdentifier);
        self::assertSame($data['fieldTypeIdentifier'], $field->fieldTypeIdentifier);
        self::assertSame($data['name'], $field->name);
        self::assertSame($data['description'], $field->description);
        self::assertEquals($languageCode, $field->languageCode);

        self::assertTrue(isset($field->fieldTypeIdentifier));
        self::assertTrue(isset($field->innerFieldDefinition));
        self::assertTrue(isset($field->name));
        self::assertTrue(isset($field->description));
        self::assertTrue(isset($field->languageCode));
        self::assertFalse(isset($field->nonExistantProperty));

        try {
            $field->nonExistentProperty;
            self::fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        self::assertEquals(
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
        [, , , $locationId, $locationRemoteId, $parentLocationId] = array_values($data);

        self::assertEquals($locationId, $location->id);
        self::assertEquals($locationRemoteId, $location->remoteId);
        self::assertEquals($parentLocationId, $location->parentLocationId);
        self::assertEquals(APILocation::STATUS_PUBLISHED, $location->status);
        self::assertEquals($data['locationPriority'], $location->priority);
        self::assertEquals($data['locationHidden'], $location->hidden);
        self::assertEquals($data['locationInvisible'], $location->invisible);
        self::assertEquals($data['locationExplicitlyHidden'], $location->explicitlyHidden);
        self::assertEquals($data['locationIsVisible'], $location->isVisible);
        self::assertEquals($data['locationPathString'], $location->pathString);
        self::assertEquals($data['locationPath'], $location->path);
        self::assertEquals($data['locationDepth'], $location->depth);
        self::assertEquals($data['locationSortField'], $location->sortField);
        self::assertEquals($data['locationSortOrder'], $location->sortOrder);
        self::assertEquals($location->contentInfo->id, $location->contentId);
        $this->assertContentInfo($location->contentInfo, $data);
        self::assertInstanceOf(APILocation::class, $location->innerLocation);
        self::assertInstanceOf(Content::class, $location->content);

        self::assertInstanceOf(Location::class, $location->parent);
        self::assertEquals($parentLocationId, $location->parent->id);

        $children = $location->getChildren();
        self::assertIsArray($children);
        self::assertCount(1, $children);
        self::assertInstanceOf(Location::class, $children[0]);
        self::assertEquals($data['childLocationId'], $children[0]->id);

        $firstChild = $location->getFirstChild();
        self::assertInstanceOf(Location::class, $firstChild);
        self::assertEquals($data['childLocationId'], $firstChild->id);

        $siblings = $location->getSiblings();
        self::assertIsArray($siblings);
        self::assertCount(1, $siblings);
        self::assertInstanceOf(Location::class, $siblings[0]);
        self::assertEquals($data['siblingLocationId'], $siblings[0]->id);

        self::assertTrue(isset($location->contentId));
        self::assertTrue(isset($location->contentInfo));
        self::assertFalse(isset($location->nonExistentProperty));

        try {
            $location->nonExistentProperty;
            self::fail('This property should not be found');
        } catch (PropertyNotFoundException $e) {
            // Do nothing
        }

        self::assertEquals(
            [
                'id' => $location->id,
                'status' => $location->status,
                'priority' => $location->priority,
                'hidden' => $location->hidden,
                'invisible' => $location->invisible,
                'explicitlyHidden' => $location->explicitlyHidden,
                'isVisible' => $location->isVisible,
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
