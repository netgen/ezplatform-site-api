<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\Content as RepoContent;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\FieldType\FieldType;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\NullLogger;

/**
 * Used for mocking Site API Content with Fields.
 */
trait ContentFieldsMockTrait
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $siteMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper[]
     */
    protected $domainObjectMapper;

    /**
     * @var \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $repositoryMock;

    /**
     * @var \eZ\Publish\Core\Repository\Values\Content\VersionInfo
     */
    protected $repoVersionInfo;

    /**
     * @var \eZ\Publish\Core\Repository\Values\Content\Content
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
     * @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $contentTypeServiceMock;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $fieldTypeServiceMock;

    /**
     * @var \eZ\Publish\SPI\FieldType\FieldType|\PHPUnit\Framework\MockObject\MockObject
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
     * @return \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    abstract public function internalGetRepoFields(): array;

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSiteMock(): MockObject
    {
        if ($this->siteMock !== null) {
            return $this->siteMock;
        }

        $this->siteMock = $this->getMockBuilder(Site::class)->getMock();

        return $this->siteMock;
    }

    /**
     * @param bool $failOnMissingFields
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected function getDomainObjectMapper(bool $failOnMissingFields = true): DomainObjectMapper
    {
        if ($this->domainObjectMapper[$failOnMissingFields] !== null) {
            return $this->domainObjectMapper[$failOnMissingFields];
        }

        $this->domainObjectMapper[$failOnMissingFields] = new DomainObjectMapper(
            $this->getSiteMock(),
            $this->getRepositoryMock(),
            $failOnMissingFields,
            new NullLogger()
        );

        return $this->domainObjectMapper[$failOnMissingFields];
    }

    /**
     * @return \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock(): MockObject
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
     * @return \eZ\Publish\API\Repository\FieldTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFieldTypeServiceMock(): MockObject
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
     * @return \eZ\Publish\SPI\FieldType\FieldType|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFieldTypeMock(): MockObject
    {
        if ($this->fieldTypeMock !== null) {
            return $this->fieldTypeMock;
        }

        $this->fieldTypeMock = $this
            ->getMockBuilder(FieldType::class)
            ->getMock();

        $this->fieldTypeMock
            ->method('isEmptyValue')
            ->willReturnCallback(static function ($field) {return empty($field->value);});

        return $this->fieldTypeMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContentTypeServiceMock(): MockObject
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
    protected function getRepoFieldDefinitions(): array
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
    abstract protected function internalGetRepoFieldDefinitions(): array;

    /**
     * @return \eZ\Publish\Core\Repository\Values\Content\Content|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepoContent(): Content
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
    protected function getRepoFields(): array
    {
        if ($this->internalFields !== null) {
            return $this->internalFields;
        }

        $this->internalFields = $this->internalGetRepoFields();

        return $this->internalFields;
    }

    protected function getRepoVersionInfo(): VersionInfo
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
