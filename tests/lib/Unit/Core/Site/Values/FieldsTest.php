<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field as RepoField;
use eZ\Publish\Core\FieldType\Integer\Value;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
use eZ\Publish\Core\Repository\Values\Content\Content as RepoContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\FieldType;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Fields;
use PHPUnit\Framework\TestCase;

/**
 * @group fields
 * Fields value unit tests.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Fields
 */
class FieldsTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected $siteMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected $domainObjectMapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected $repositoryMock;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\VersionInfo
     */
    protected $repoVersionInfo;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected $repoContent;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    protected $internalFields;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\SPI\FieldType\FieldType
     */
    private $fieldTypeMock;

    public function testFieldsObjectIsCountable()
    {
        $fields = $this->getFieldsObjectUnderTest();

        $this->assertCount(1, $fields);
    }

    protected function getFieldsObjectUnderTest()
    {
        return new Fields(
            $this->getMockedContent(),
            $this->getDomainObjectMapper(),
            true
        );
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected function getMockedContent()
    {
        return new Content(
            [
                'site' => $this->getSiteMock(),
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'innerVersionInfo' => $this->getRepoVersionInfo(),
                'innerContent' => $this->getRepoContent(),
            ],
            true,
            null
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSiteMock()
    {
        if ($this->siteMock !== null) {
            return $this->siteMock;
        }

        $this->siteMock = $this->getMockBuilder(Site::class)->getMock();

        return $this->siteMock;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected function getDomainObjectMapper()
    {
        if ($this->domainObjectMapper !== null) {
            return $this->domainObjectMapper;
        }

        $this->domainObjectMapper = new DomainObjectMapper(
            $this->getSiteMock(),
            $this->getRepositoryMock(),
            true
        );

        return $this->domainObjectMapper;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected function getRepositoryMock()
    {
        if ($this->repositoryMock !== null) {
            return $this->repositoryMock;
        }

        $this->repositoryMock = $this
            ->getMockBuilder(CoreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repoContent = $this->getRepoContent();
        $this->repositoryMock->method('sudo')->willReturn($repoContent);

        $contentTypeServiceMock = $this->getContentTypeServiceMock();
        $this->repositoryMock->method('getContentTypeService')->willReturn($contentTypeServiceMock);

        $fieldTypeServiceMock = $this->getFieldTypeServiceMock();
        $this->repositoryMock->method('getFieldTypeService')->willReturn($fieldTypeServiceMock);

        return $this->repositoryMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected function getFieldTypeServiceMock()
    {
        if (null !== $this->fieldTypeServiceMock) {
            return $this->fieldTypeServiceMock;
        }

        $this->fieldTypeServiceMock = $this
            ->getMockBuilder(FieldTypeService::class)
            ->getMock();

        $fieldTypeMock = $this->getFieldTypeMock();
        $this->fieldTypeServiceMock
            ->method('getFieldType')
            ->willReturn($fieldTypeMock);

        return $this->fieldTypeServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\SPI\FieldType\FieldType
     */
    protected function getFieldTypeMock()
    {
        if (null !== $this->fieldTypeMock) {
            return $this->fieldTypeMock;
        }

        $this->fieldTypeMock = $this
            ->getMockBuilder(FieldType::class)
            ->getMock();

        $this->fieldTypeMock
            ->method('isEmptyValue')
            ->willReturn(false);

        return $this->fieldTypeMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeServiceMock()
    {
        if (null !== $this->contentTypeServiceMock) {
            return $this->contentTypeServiceMock;
        }

        $this->contentTypeServiceMock = $this->getMockBuilder(ContentTypeService::class)->getMock();

        $this->contentTypeServiceMock
            ->method('loadContentType')
            ->with('contentTypeId')
            ->willReturn(
                new ContentType([
                    'id' => 42,
                    'identifier' => 'test',
                    'fieldDefinitions' => [
                        new FieldDefinition([
                            'id' => 1,
                            'identifier' => 'first',
                        ]),
                    ],
                ])
            );

        return $this->contentTypeServiceMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\Values\Content\Content
     */
    protected function getRepoContent()
    {
        if ($this->repoContent !== null) {
            return $this->repoContent;
        }

        $repoVersionInfo = $this->getRepoVersionInfo();

        $this->repoContent = new RepoContent([
            'versionInfo' => $repoVersionInfo,
            'internalFields' => $this->getInternalFields(),
        ]);

        return $this->repoContent;
    }

    protected function getInternalFields()
    {
        if ($this->internalFields !== null) {
            return $this->internalFields;
        }

        $this->internalFields = [
            new RepoField([
                'id' => 1,
                'fieldDefIdentifier' => 'first',
                'value' => new Value(1),
                'languageCode' => 'eng-GB',
                'fieldTypeIdentifier' => 'ezinteger',
            ]),
        ];

        return $this->internalFields;
    }

    protected function getRepoVersionInfo()
    {
        if ($this->repoVersionInfo !== null) {
            return $this->repoVersionInfo;
        }

        $repoContentInfo = new RepoContentInfo([
            'ownerId' => 'ownerId',
            'contentTypeId' => 'contentTypeId',
            'mainLanguageCode' => 'eng-GB',
        ]);

        $this->repoVersionInfo = new VersionInfo([
            'contentInfo' => $repoContentInfo,
        ]);

        return $this->repoVersionInfo;
    }
}
