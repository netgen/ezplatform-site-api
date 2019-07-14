<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * Content value unit tests.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Content
 */
class ContentTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected $siteMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected $domainObjectMapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\ContentService
     */
    protected $contentServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected $fieldTypeServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\LoadService
     */
    protected $loadServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\UserService
     */
    protected $userServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    protected $queryTypeRegistryMock;

    public function setUp(): void
    {
        $this->getSiteMock();
        $this->getDomainObjectMapper();
        $this->getLoadServiceMock();
        $this->getUserServiceMock();
        $this->getRepositoryMock();

        parent::setUp();
    }

    public function testContentOwnerExists(): void
    {
        $content = $this->getMockedContent();
        $ownerMock = $this->getMockBuilder(APIContent::class)->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('sudo')
            ->willReturn($ownerMock);

        $this->assertSame($ownerMock, $content->owner);
    }

    public function testContentOwnerExistsRepeated(): void
    {
        $content = $this->getMockedContent();
        $ownerMock = $this->getMockBuilder(APIContent::class)->getMock();

        $this->repositoryMock->expects($this->once())
            ->method('sudo')
            ->willReturn($ownerMock);

        $this->assertSame($ownerMock, $content->owner);
        $this->assertSame($ownerMock, $content->owner);
    }

    public function testContentOwnerDoesNotExist(): void
    {
        $content = $this->getMockedContent();

        $this->repositoryMock->expects($this->once())
            ->method('sudo')
            ->willReturn(null);

        $this->assertNull($content->owner);
    }

    public function testContentOwnerDoesNotExistRepeated(): void
    {
        $content = $this->getMockedContent();

        $this->repositoryMock->expects($this->once())
            ->method('sudo')
            ->willReturn(null);

        $this->assertNull($content->owner);
        $this->assertNull($content->owner);
    }

    public function testContentInnerOwnerUserExists(): void
    {
        $content = $this->getMockedContent();
        $ownerUserMock = $this->getMockBuilder(User::class)->getMock();

        $this
            ->userServiceMock
            ->expects($this->once())
            ->method('loadUser')
            ->with('ownerId')
            ->willReturn($ownerUserMock);

        $this->assertSame($ownerUserMock, $content->innerOwnerUser);
    }

    public function testContentInnerOwnerUserExistsRepeated(): void
    {
        $content = $this->getMockedContent();
        $ownerUserMock = $this->getMockBuilder(User::class)->getMock();

        $this
            ->userServiceMock
            ->expects($this->once())
            ->method('loadUser')
            ->with('ownerId')
            ->willReturn($ownerUserMock);

        $this->assertSame($ownerUserMock, $content->innerOwnerUser);
        $this->assertSame($ownerUserMock, $content->innerOwnerUser);
    }

    public function testContentInnerOwnerUserDoesNotExist(): void
    {
        $content = $this->getMockedContent();

        $this
            ->userServiceMock
            ->expects($this->once())
            ->method('loadUser')
            ->with('ownerId')
            ->willThrowException(
                new NotFoundException('User', 'ownerId')
            );

        $this->assertNull($content->innerOwnerUser);
    }

    public function testContentInnerOwnerUserDoesNotExistRepeated(): void
    {
        $content = $this->getMockedContent();

        $this
            ->userServiceMock
            ->expects($this->once())
            ->method('loadUser')
            ->with('ownerId')
            ->willThrowException(
                new NotFoundException('User', 'ownerId')
            );

        $this->assertNull($content->innerOwnerUser);
        $this->assertNull($content->innerOwnerUser);
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Values\Content
     */
    protected function getMockedContent(): APIContent
    {
        return new Content(
            [
                'site' => $this->getSiteMock(),
                'domainObjectMapper' => $this->getDomainObjectMapper(),
                'repository' => $this->getRepositoryMock(),
                'innerVersionInfo' => new VersionInfo([
                    'contentInfo' => new RepoContentInfo([
                        'ownerId' => 'ownerId',
                        'contentTypeId' => 'contentTypeId',
                    ]),
                ]),
                'languageCode' => 'eng-GB',
            ],
            true,
            new NullLogger()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSiteMock(): MockObject
    {
        if (null !== $this->siteMock) {
            return $this->siteMock;
        }

        $this->siteMock = $this
            ->getMockBuilder(Site::class)
            ->getMock();

        $this->siteMock
            ->method('getLoadService')
            ->willReturn($this->getLoadServiceMock());

        return $this->siteMock;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected function getDomainObjectMapper(): DomainObjectMapper
    {
        if (null !== $this->domainObjectMapper) {
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Netgen\EzPlatformSiteApi\API\LoadService
     */
    protected function getLoadServiceMock(): MockObject
    {
        if (null !== $this->loadServiceMock) {
            return $this->loadServiceMock;
        }

        $this->loadServiceMock = $this
            ->getMockBuilder(LoadService::class)
            ->getMock();

        return $this->loadServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\ContentService
     */
    protected function getContentServiceMock(): MockObject
    {
        if (null !== $this->contentServiceMock) {
            return $this->contentServiceMock;
        }

        $this->contentServiceMock = $this
            ->getMockBuilder(ContentService::class)
            ->getMock();

        return $this->contentServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeServiceMock(): MockObject
    {
        if (null !== $this->contentTypeServiceMock) {
            return $this->contentTypeServiceMock;
        }

        $this->contentTypeServiceMock = $this
            ->getMockBuilder(ContentTypeService::class)
            ->getMock();

        $this->contentTypeServiceMock
            ->method('loadContentType')
            ->with('contentTypeId')
            ->willReturn(new ContentType([
                'fieldDefinitions' => [],
            ]));

        return $this->contentTypeServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected function getFieldTypeServiceMock(): MockObject
    {
        if (null !== $this->fieldTypeServiceMock) {
            return $this->fieldTypeServiceMock;
        }

        $this->fieldTypeServiceMock = $this
            ->getMockBuilder(FieldTypeService::class)
            ->getMock();

        return $this->fieldTypeServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\UserService
     */
    protected function getUserServiceMock(): MockObject
    {
        if (null !== $this->userServiceMock) {
            return $this->userServiceMock;
        }

        $this->userServiceMock = $this
            ->getMockBuilder(UserService::class)
            ->getMock();

        return $this->userServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\Repository
     */
    protected function getRepositoryMock(): MockObject
    {
        if (null !== $this->repositoryMock) {
            return $this->repositoryMock;
        }

        $this->repositoryMock = $this
            ->getMockBuilder(CoreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock->method('getContentService')->willReturn($this->getContentServiceMock());
        $this->repositoryMock->method('getContentTypeService')->willReturn($this->getContentTypeServiceMock());
        $this->repositoryMock->method('getFieldTypeService')->willReturn($this->getFieldTypeServiceMock());
        $this->repositoryMock->method('getUserService')->willReturn($this->getUserServiceMock());

        return $this->repositoryMock;
    }
}
