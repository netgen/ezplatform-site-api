<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository as CoreRepository;
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
 *
 * @internal
 */
final class ContentTest extends TestCase
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $siteMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected $domainObjectMapper;

    /**
     * @var \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $contentServiceMock;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $contentTypeServiceMock;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $fieldTypeServiceMock;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\LoadService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loadServiceMock;

    /**
     * @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $userServiceMock;

    /**
     * @var \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $repositoryMock;

    /**
     * @var \eZ\Publish\Core\QueryType\QueryTypeRegistry|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $queryTypeRegistryMock;

    protected function setUp(): void
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
     * @return \Netgen\EzPlatformSiteApi\API\Site|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSiteMock(): MockObject
    {
        if ($this->siteMock !== null) {
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
     * @return \Netgen\EzPlatformSiteApi\API\LoadService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getLoadServiceMock(): MockObject
    {
        if ($this->loadServiceMock !== null) {
            return $this->loadServiceMock;
        }

        $this->loadServiceMock = $this
            ->getMockBuilder(LoadService::class)
            ->getMock();

        return $this->loadServiceMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContentServiceMock(): MockObject
    {
        if ($this->contentServiceMock !== null) {
            return $this->contentServiceMock;
        }

        $this->contentServiceMock = $this
            ->getMockBuilder(ContentService::class)
            ->getMock();

        return $this->contentServiceMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContentTypeServiceMock(): MockObject
    {
        if ($this->contentTypeServiceMock !== null) {
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

        return $this->fieldTypeServiceMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getUserServiceMock(): MockObject
    {
        if ($this->userServiceMock !== null) {
            return $this->userServiceMock;
        }

        $this->userServiceMock = $this
            ->getMockBuilder(UserService::class)
            ->getMock();

        return $this->userServiceMock;
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

        $this->repositoryMock->method('getContentService')->willReturn($this->getContentServiceMock());
        $this->repositoryMock->method('getContentTypeService')->willReturn($this->getContentTypeServiceMock());
        $this->repositoryMock->method('getFieldTypeService')->willReturn($this->getFieldTypeServiceMock());
        $this->repositoryMock->method('getUserService')->willReturn($this->getUserServiceMock());

        return $this->repositoryMock;
    }
}
