<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use Netgen\EzPlatformSiteApi\API\Values\Content;
use eZ\Publish\API\Repository\Tests\BaseTest as APIBaseTest;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use DateTime;

/**
 * Base class for API integration tests.
 */
class BaseTest extends APIBaseTest
{
    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSite()
    {
        return $this->getSetupFactory()->getServiceContainer()->get('netgen.ezplatform_site.site');
    }

    public function getPrimaryLanguageMatchData()
    {
        $data = [
            'name' => 'Contact Us',
            'contentId' => 58,
            'contentRemoteId' => 'f8cc7a4cf8a964a1a0ea6666f5da7d0d',
            'locationId' => 60,
            'locationRemoteId' => '86bf306624668ee9b8b979b0d56f7e0d',
            'parentLocationId' => 2,
            'locationPriority' => -2,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationPathString' => '/1/2/60/',
            'locationPath' => [1, 2, 60],
            'locationDepth' => 2,
            'locationSortField' => APILocation::SORT_FIELD_PRIORITY,
            'locationSortOrder' => APILocation::SORT_ORDER_ASC,
            'contentTypeIdentifier' => 'feedback_form',
            'contentTypeId' => 20,
            'sectionId' => 1,
            'currentVersionNo' => 1,
            'published' => true,
            'ownerId' => 14,
            'modificationDateTimestamp' => 1332927282,
            'publishedDateTimestamp' => 1332927205,
            'alwaysAvailable' => false,
            'mainLanguageCode' => 'eng-GB',
            'mainLocationId' => 60,
            'contentTypeName' => 'Feedback form',
            'contentTypeDescription' => '',
            'languageCode' => 'eng-GB',
            'fields' => [
                'description' => [
                    'fieldTypeIdentifier' => 'ezrichtext',
                    'isEmpty' => false,
                ],
                'email' => [
                    'fieldTypeIdentifier' => 'ezemail',
                    'isEmpty' => false,
                ],
                'message' => [
                    'fieldTypeIdentifier' => 'eztext',
                    'isEmpty' => false,
                ],
                'name' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'recipient' => [
                    'fieldTypeIdentifier' => 'ezemail',
                    'isEmpty' => false,
                ],
                'sender_name' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'subject' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
            ],
            'isFound' => true,
        ];

        return [
            0 => [
                $data,
            ],
        ];
    }

    public function getSecondaryLanguageMatchData()
    {
        $data = [
            'name' => 'Das Titel',
            'contentId' => 54,
            'contentRemoteId' => '8b8b22fe3c6061ed500fbd2b377b885f',
            'locationId' => 56,
            'locationRemoteId' => '772da20ecf88b3035d73cbdfcea0f119',
            'parentLocationId' => 58,
            'locationPriority' => 0,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationPathString' => '/1/58/56/',
            'locationPath' => [1, 58, 56],
            'locationDepth' => 2,
            'locationSortField' => APILocation::SORT_FIELD_PATH,
            'locationSortOrder' => APILocation::SORT_ORDER_ASC,
            'contentTypeIdentifier' => 'template_look',
            'contentTypeId' => 15,
            'sectionId' => 5,
            'currentVersionNo' => 3,
            'published' => true,
            'ownerId' => 14,
            'modificationDateTimestamp' => 100,
            'publishedDateTimestamp' => 1082016652,
            'alwaysAvailable' => false,
            'mainLanguageCode' => 'eng-US',
            'mainLocationId' => 56,
            'contentTypeName' => '',
            'contentTypeDescription' => '',
            'languageCode' => 'ger-DE',
            'fields' => [
                'email' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'footer_script' => [
                    'fieldTypeIdentifier' => 'eztext',
                    'isEmpty' => true,
                ],
                'footer_text' => [
                    'fieldTypeIdentifier' => 'eztext',
                    'isEmpty' => true,
                ],
                'hide_powered_by' => [
                    'fieldTypeIdentifier' => 'ezboolean',
                    'isEmpty' => true,
                ],
                'image' => [
                    'fieldTypeIdentifier' => 'ezimage',
                    'isEmpty' => true,
                ],
                'login_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'logout_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'meta_data' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'my_profile_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'register_user_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'rss_feed' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'shopping_basket_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'site_map_url' => [
                    'fieldTypeIdentifier' => 'ezurl',
                    'isEmpty' => true,
                ],
                'site_settings_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => true,
                ],
                'sitestyle' => [
                    'fieldTypeIdentifier' => 'ezpackage',
                    'isEmpty' => true,
                ],
                'siteurl' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'tag_cloud_url' => [
                    'fieldTypeIdentifier' => 'ezurl',
                    'isEmpty' => true,
                ],
                'title' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
            ],
            'isFound' => true,
        ];

        return [
            0 => [
                $data,
            ],
        ];
    }

    public function getExplicitVersionAndLanguageMatchData()
    {
        $data = [
            'name' => 'eZ Publish Demo Design (without demo content)',
            'contentId' => 54,
            'contentRemoteId' => '8b8b22fe3c6061ed500fbd2b377b885f',
            'locationId' => 56,
            'locationRemoteId' => '772da20ecf88b3035d73cbdfcea0f119',
            'parentLocationId' => 58,
            'locationPriority' => 0,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationPathString' => '/1/58/56/',
            'locationPath' => [1, 58, 56],
            'locationDepth' => 2,
            'locationSortField' => APILocation::SORT_FIELD_PATH,
            'locationSortOrder' => APILocation::SORT_ORDER_ASC,
            'contentTypeIdentifier' => 'template_look',
            'contentTypeId' => 15,
            'sectionId' => 5,
            'currentVersionNo' => 3,
            'published' => true,
            'ownerId' => 14,
            'modificationDateTimestamp' => 100,
            'publishedDateTimestamp' => 1082016652,
            'alwaysAvailable' => false,
            'mainLanguageCode' => 'eng-US',
            'mainLocationId' => 56,
            'contentTypeName' => 'Template look',
            'contentTypeDescription' => '',
            'languageCode' => 'eng-US',
            'fields' => [
                'email' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'footer_script' => [
                    'fieldTypeIdentifier' => 'eztext',
                    'isEmpty' => true,
                ],
                'footer_text' => [
                    'fieldTypeIdentifier' => 'eztext',
                    'isEmpty' => false,
                ],
                'hide_powered_by' => [
                    'fieldTypeIdentifier' => 'ezboolean',
                    'isEmpty' => true,
                ],
                'image' => [
                    'fieldTypeIdentifier' => 'ezimage',
                    'isEmpty' => false,
                ],
                'login_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'logout_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'meta_data' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'my_profile_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'register_user_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'rss_feed' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'shopping_basket_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'site_map_url' => [
                    'fieldTypeIdentifier' => 'ezurl',
                    'isEmpty' => false,
                ],
                'site_settings_label' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'sitestyle' => [
                    'fieldTypeIdentifier' => 'ezpackage',
                    'isEmpty' => true,
                ],
                'siteurl' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
                'tag_cloud_url' => [
                    'fieldTypeIdentifier' => 'ezurl',
                    'isEmpty' => false,
                ],
                'title' => [
                    'fieldTypeIdentifier' => 'ezinisetting',
                    'isEmpty' => true,
                ],
            ],
            'isFound' => true,
            'loadVersionNo' => 2,
            'loadLanguageCode' => 'eng-US',
        ];

        return [
            0 => [
                $data,
            ],
        ];
    }

    public function getAlwaysAvailableLanguageMatchData()
    {
        $data = [
            'name' => 'Users',
            'contentId' => 4,
            'contentRemoteId' => 'f5c88a2209584891056f987fd965b0ba',
            'locationId' => 5,
            'locationRemoteId' => '3f6d92f8044aed134f32153517850f5a',
            'parentLocationId' => 1,
            'locationPriority' => 0,
            'locationHidden' => false,
            'locationInvisible' => false,
            'locationPathString' => '/1/5/',
            'locationPath' => [1, 5],
            'locationDepth' => 1,
            'locationSortField' => APILocation::SORT_FIELD_PATH,
            'locationSortOrder' => APILocation::SORT_ORDER_ASC,
            'contentTypeIdentifier' => 'user_group',
            'contentTypeId' => 3,
            'sectionId' => 2,
            'currentVersionNo' => 1,
            'published' => true,
            'ownerId' => 14,
            'modificationDateTimestamp' => 1033917596,
            'publishedDateTimestamp' => 1033917596,
            'alwaysAvailable' => true,
            'mainLanguageCode' => 'eng-US',
            'mainLocationId' => 5,
            'contentTypeName' => 'User group',
            'contentTypeDescription' => '',
            'languageCode' => 'eng-US',
            'fields' => [
                'description' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
                'name' => [
                    'fieldTypeIdentifier' => 'ezstring',
                    'isEmpty' => false,
                ],
            ],
            'isFound' => true,
        ];

        return [
            0 => [
                $data,
            ],
        ];
    }

    public function getNoLanguageMatchData()
    {
        $data = [
            'name' => 'Common INI settings',
            'contentId' => 52,
            'contentRemoteId' => '27437f3547db19cf81a33c92578b2c89',
            'locationId' => 54,
            'locationRemoteId' => 'fa9f3cff9cf90ecfae335718dcbddfe2',
            'parentLocationId' => null,
            'locationPriority' => null,
            'locationHidden' => null,
            'locationInvisible' => null,
            'locationPathString' => null,
            'locationPath' => null,
            'locationDepth' => null,
            'locationSortField' => null,
            'locationSortOrder' => null,
            'contentTypeIdentifier' => null,
            'contentTypeId' => null,
            'sectionId' => null,
            'currentVersionNo' => null,
            'published' => null,
            'ownerId' => null,
            'modificationDateTimestamp' => null,
            'publishedDateTimestamp' => null,
            'alwaysAvailable' => null,
            'mainLanguageCode' => null,
            'mainLocationId' => null,
            'contentTypeName' => null,
            'contentTypeDescription' => null,
            'languageCode' => null,
            'fields' => null,
            'isFound' => false,
        ];

        return [
            0 => [
                $data,
            ],
        ];
    }

    protected function doTestLoadContent($data)
    {
        list(, $contentId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $content = $loadService->loadContent($contentId);
        $this->assertContent($content, $data);
    }

    protected function doTestLoadContentByRemoteId($data)
    {
        list(, , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $content = $loadService->loadContentByRemoteId($remoteId);
        $this->assertContent($content, $data);
    }

    protected function doTestLoadContentInfo($data)
    {
        list(, $contentId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $contentInfo = $loadService->loadContentInfo($contentId);
        $this->assertContentInfo($contentInfo, $data);
    }

    protected function doTestLoadContentInfoByRemoteId($data)
    {
        list(, , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $contentInfo = $loadService->loadContentInfoByRemoteId($remoteId);
        $this->assertContentInfo($contentInfo, $data);
    }

    protected function doTestLoadLocation($data)
    {
        list(, , , $locationId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $location = $loadService->loadLocation($locationId);
        $this->assertLocation($location, $data);
    }

    protected function doTestLoadLocationByRemoteId($data)
    {
        list(, , , , $remoteId) = array_values($data);
        $loadService = $this->getSite()->getLoadService();
        $location = $loadService->loadLocationByRemoteId($remoteId);
        $this->assertLocation($location, $data);
    }

    protected function assertContent($content, $data)
    {
        list($name, $contentId, , $locationId) = array_values($data);

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $this->assertInstanceOf('\Netgen\EzPlatformSiteApi\API\Values\Content', $content);

        $this->assertSame($contentId, $content->id);
        $this->assertSame($locationId, $content->mainLocationId);
        $this->assertSame($name, $content->name);
        $this->assertContentInfo($content->contentInfo, $data);
        $this->assertFields($content, $data);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\Content', $content->innerContent);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\VersionInfo', $content->versionInfo);
    }

    protected function assertContentInfo($contentInfo, $data)
    {
        list($name, $contentId, $contentRemoteId, $locationId) = array_values($data);

        /** @var \Netgen\EzPlatformSiteApi\API\Values\ContentInfo $contentInfo */
        $this->assertInstanceOf('\Netgen\EzPlatformSiteApi\API\Values\ContentInfo', $contentInfo);

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
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\ContentInfo', $contentInfo->innerContentInfo);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\ContentType\ContentType', $contentInfo->innerContentType);
    }

    protected function assertFields(Content $content, $data)
    {
        $this->assertInternalType('array', $content->fields);
        $this->assertCount(count($data['fields']), $content->fields);

        foreach ($content->fields as $identifier => $field) {
            $this->assertInstanceOf('\Netgen\EzPlatformSiteApi\API\Values\Field', $field);
            $this->assertInstanceOf('\eZ\Publish\SPI\FieldType\Value', $field->value);
            $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\Field', $field->innerField);

            $fieldById = $content->getFieldById($field->id);
            $fieldByIdentifier = $content->getField($identifier);

            $this->assertSame($field, $fieldById);
            $this->assertSame($fieldById, $fieldByIdentifier);

            $fieldValueById = $content->getFieldValueById($field->id);
            $fieldValueByIdentifier = $content->getFieldValue($identifier);

            $this->assertSame($field->value, $fieldValueById);
            $this->assertSame($fieldValueById, $fieldValueByIdentifier);

            $this->assertSame($content, $field->content);
        }

        foreach ($data['fields'] as $identifier => $fieldData) {
            $this->assertField($content, $identifier, $data['languageCode'], $fieldData);
        }
    }

    protected function assertField(Content $content, $identifier, $languageCode, $data)
    {
        $field = $content->getField($identifier);

        $this->assertSame($data['isEmpty'], $field->isEmpty());
        $this->assertSame($identifier, $field->fieldDefIdentifier);
        $this->assertSame($data['fieldTypeIdentifier'], $field->fieldTypeIdentifier);
        $this->assertEquals($languageCode, $field->languageCode);
    }

    protected function assertLocation($location, $data)
    {
        list(, , , $locationId, $locationRemoteId, $parentLocationId) = array_values($data);

        /** @var \Netgen\EzPlatformSiteApi\API\Values\Location $location */
        $this->assertInstanceOf('\Netgen\EzPlatformSiteApi\API\Values\Location', $location);

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
        $this->assertContentInfo($location->contentInfo, $data);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\Location', $location->innerLocation);
    }

    protected function createSecondaryTranslationFallback()
    {
        $templateLookContentTypeId = 15;
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentType($templateLookContentTypeId);
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);
        $contentTypeUpdateStruct = $contentTypeService->newContentTypeUpdateStruct();
        $contentTypeUpdateStruct->nameSchema = '<my_profile_label>';
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $contentTypeUpdateStruct);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $demoDesignContentId = 54;
        $contentService = $this->getRepository()->getContentService();
        $contentInfo = $contentService->loadContentInfo($demoDesignContentId);
        $draft = $contentService->createContentDraft($contentInfo);
        $contentUpdateStruct = $contentService->newContentUpdateStruct();
        $contentUpdateStruct->setField('my_profile_label', 'Das Titel', 'ger-DE');
        $updatedDraft = $contentService->updateContent($draft->versionInfo, $contentUpdateStruct);
        $content = $contentService->publishVersion($updatedDraft->versionInfo);

        $contentMetadataUpdateStruct = $contentService->newContentMetadataUpdateStruct();
        $contentMetadataUpdateStruct->modificationDate = new DateTime('@100');
        $contentService->updateContentMetadata($content->contentInfo, $contentMetadataUpdateStruct);
    }
}
