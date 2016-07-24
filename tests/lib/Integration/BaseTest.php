<?php

namespace Netgen\EzPlatformSite\Tests\Integration;

use eZ\Publish\API\Repository\Tests\BaseTest as APIBaseTest;
use Netgen\EzPlatformSite\API\Values\Content;
use Netgen\EzPlatformSite\API\Values\Field;

/**
 * Base class for API integration tests.
 */
class BaseTest extends APIBaseTest
{
    /**
     * @return \Netgen\EzPlatformSite\API\Site
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
            'contentTypeIdentifier' => 'feedback_form',
            'languageCode' => 'eng-GB',
            'fields' => [
                'description' => new Field(
                    [
                        'identifier' => 'description',
                        'typeIdentifier' => 'ezrichtext',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'email' => new Field(
                    [
                        'identifier' => 'email',
                        'typeIdentifier' => 'ezemail',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'message' => new Field(
                    [
                        'identifier' => 'message',
                        'typeIdentifier' => 'eztext',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'name' => new Field(
                    [
                        'identifier' => 'name',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'recipient' => new Field(
                    [
                        'identifier' => 'recipient',
                        'typeIdentifier' => 'ezemail',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'sender_name' => new Field(
                    [
                        'identifier' => 'sender_name',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'subject' => new Field(
                    [
                        'identifier' => 'subject',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
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
            'contentTypeIdentifier' => 'template_look',
            'languageCode' => 'ger-DE',
            'fields' => [
                'email' => new Field(
                    [
                        'identifier' => 'email',
                        'typeIdentifier' => 'ezinisetting',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'footer_script' => new Field(
                    [
                        'identifier' => 'footer_script',
                        'typeIdentifier' => 'eztext',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'footer_text' => new Field(
                    [
                        'identifier' => 'footer_text',
                        'typeIdentifier' => 'eztext',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'hide_powered_by' => new Field(
                    [
                        'identifier' => 'hide_powered_by',
                        'typeIdentifier' => 'ezboolean',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'image' => new Field(
                    [
                        'identifier' => 'image',
                        'typeIdentifier' => 'ezimage',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'login_label' => new Field(
                    [
                        'identifier' => 'login_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'logout_label' => new Field(
                    [
                        'identifier' => 'logout_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'meta_data' => new Field(
                    [
                        'identifier' => 'meta_data',
                        'typeIdentifier' => 'ezinisetting',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'my_profile_label' => new Field(
                    [
                        'identifier' => 'my_profile_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'register_user_label' => new Field(
                    [
                        'identifier' => 'register_user_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'rss_feed' => new Field(
                    [
                        'identifier' => 'rss_feed',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'shopping_basket_label' => new Field(
                    [
                        'identifier' => 'shopping_basket_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'site_map_url' => new Field(
                    [
                        'identifier' => 'site_map_url',
                        'typeIdentifier' => 'ezurl',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'site_settings_label' => new Field(
                    [
                        'identifier' => 'site_settings_label',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'sitestyle' => new Field(
                    [
                        'identifier' => 'sitestyle',
                        'typeIdentifier' => 'ezpackage',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'siteurl' => new Field(
                    [
                        'identifier' => 'siteurl',
                        'typeIdentifier' => 'ezinisetting',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'tag_cloud_url' => new Field(
                    [
                        'identifier' => 'tag_cloud_url',
                        'typeIdentifier' => 'ezurl',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
                'title' => new Field(
                    [
                        'identifier' => 'title',
                        'typeIdentifier' => 'ezinisetting',
                        'isEmpty' => true,
                        'value' => false,
                    ]
                ),
            ],
            'isFound' => true,
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
            'contentTypeIdentifier' => 'user_group',
            'languageCode' => 'eng-US',
            'fields' => [
                'description' => new Field(
                    [
                        'identifier' => 'description',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
                'name' => new Field(
                    [
                        'identifier' => 'name',
                        'typeIdentifier' => 'ezstring',
                        'isEmpty' => false,
                        'value' => true,
                    ]
                ),
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
            'contentTypeIdentifier' => null,
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

        /** @var \Netgen\EzPlatformSite\API\Values\Content $content */
        $this->assertInstanceOf('\Netgen\EzPlatformSite\API\Values\Content', $content);

        $this->assertSame($contentId, $content->id);
        $this->assertSame($locationId, $content->mainLocationId);
        $this->assertSame($name, $content->name);
        $this->assertContentInfo($content->contentInfo, $data);
        $this->assertFields($content, $data);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\Content', $content->innerContent);
    }

    protected function assertContentInfo($contentInfo, $data)
    {
        list($name, $contentId, , $locationId, , $contentTypeIdentifier, $languageCode) = array_values($data);

        /** @var \Netgen\EzPlatformSite\API\Values\ContentInfo $contentInfo */
        $this->assertInstanceOf('\Netgen\EzPlatformSite\API\Values\ContentInfo', $contentInfo);

        $this->assertEquals($contentId, $contentInfo->id);
        $this->assertEquals($locationId, $contentInfo->mainLocationId);
        $this->assertEquals($contentTypeIdentifier, $contentInfo->contentTypeIdentifier);
        $this->assertEquals($name, $contentInfo->name);
        $this->assertEquals($languageCode, $contentInfo->languageCode);
        $this->assertInstanceOf('\eZ\Publish\API\Repository\Values\Content\ContentInfo', $contentInfo->innerContentInfo);
    }

    protected function assertFields(Content $content, $data)
    {
        list(, , , , , , , $expectedFields) = array_values($data);

        $this->assertInternalType('array', $content->fields);
        $this->assertCount(count($expectedFields), $content->fields);

        foreach ($content->fields as $identifier => $field) {
            $this->assertInstanceOf('\Netgen\EzPlatformSite\API\Values\Field', $field);
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
        }

        $this->assertEquals(
            $expectedFields,
            $this->normalizeFields($content->fields)
        );
    }

    protected function assertLocation($location, $data)
    {
        list(, , , $locationId) = array_values($data);

        /**
         * @var \Netgen\EzPlatformSite\API\Values\Location
         */
        $this->assertInstanceOf('\Netgen\EzPlatformSite\API\Values\Location', $location);

        $this->assertEquals($locationId, $location->id);
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
        $contentService->publishVersion($updatedDraft->versionInfo);
    }

    protected function normalizeFields(array $fields)
    {
        $normalizedFields = [];

        foreach ($fields as $identifier => $field) {
            $normalizedFields[$identifier] = new Field(
                [
                    'identifier' => $field->identifier,
                    'typeIdentifier' => $field->typeIdentifier,
                    'isEmpty' => $field->isEmpty,
                    'value' => $field->isEmpty ? false : true,
                ]
            );
        }

        ksort($normalizedFields, SORT_STRING);

        return $normalizedFields;
    }
}
