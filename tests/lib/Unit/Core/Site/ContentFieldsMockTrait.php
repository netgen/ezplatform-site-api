<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
use eZ\Publish\Core\Repository\Values\Content\Content as RepoContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\FieldType\FieldType;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use PHPUnit\Framework\MockObject\MockBuilder;
use Psr\Log\NullLogger;

/**
 * Used for mocking Site API Content with Fields.
 */
trait ContentFieldsMockTrait
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
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[]
     */
    protected $fieldDefinitions;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected $fieldTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\SPI\FieldType\FieldType
     */
    protected $fieldTypeMock;

    /**
     * @see \PHPUnit\Framework\TestCase
     *
     * @param string|string[] $className
     *
     * @return \PHPUnit\Framework\MockObject\MockBuilder
     */
    abstract public function getMockBuilder($className): MockBuilder;

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
            true,
            new NullLogger()
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


        $this->repositoryMock->method('getContentService')->willReturn(false);
        $this->repositoryMock->method('getUserService')->willReturn(false);

        return $this->repositoryMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected function getFieldTypeServiceMock()
    {
        if ($this->fieldTypeServiceMock !== null) {
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
        if ($this->fieldTypeMock !== null) {
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
        if ($this->contentTypeServiceMock !== null) {
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
                    'fieldDefinitions' => $this->getRepoFieldDefinitions(),
                ])
            );

        return $this->contentTypeServiceMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[]
     */
    protected function getRepoFieldDefinitions()
    {
        if ($this->fieldDefinitions !== null) {
            return $this->fieldDefinitions;
        }

        $this->fieldDefinitions = $this->internalGetRepoFieldDefinitions();

        return $this->fieldDefinitions;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition[]
     */
    abstract protected function internalGetRepoFieldDefinitions();

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
            'internalFields' => $this->getRepoFields(),
        ]);

        return $this->repoContent;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    protected function getRepoFields()
    {
        if ($this->internalFields !== null) {
            return $this->internalFields;
        }

        $this->internalFields = $this->internalGetRepoFields();

        return $this->internalFields;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    abstract public function internalGetRepoFields();

    protected function getRepoVersionInfo()
    {
        if ($this->repoVersionInfo !== null) {
            return $this->repoVersionInfo;
        }

        $repoContentInfo = new RepoContentInfo([
            'id' => 1,
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
