<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\Values;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as RepoContentInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Content as APIContent;
use Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use PHPUnit\Framework\TestCase;

/**
 * Content value unit tests.
 *
 * @see \Netgen\EzPlatformSiteApi\API\Values\Content
 */
class ContentTest extends TestCase
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
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentService
     */
    protected $contentServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected $fieldTypeServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\LoadService
     */
    protected $loadServiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\UserService
     */
    protected $userServiceMock;

    public function setUp()
    {
        $this->getSiteMock();
        $this->getDomainObjectMapper();
        $this->getLoadServiceMock();
        $this->getUserServiceMock();

        parent::setUp();
    }

    public function testContentOwnerExists()
    {
        $content = $this->getMockedContent();
        $ownerMock = $this->getMockBuilder(APIContent::class)->getMock();

        $this
            ->loadServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with('ownerId')
            ->willReturn($ownerMock);

        $this->assertSame($ownerMock, $content->owner);
    }

    public function testContentOwnerExistsRepeated()
    {
        $content = $this->getMockedContent();
        $ownerMock = $this->getMockBuilder(APIContent::class)->getMock();

        $this
            ->loadServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with('ownerId')
            ->willReturn($ownerMock);

        $this->assertSame($ownerMock, $content->owner);
        $this->assertSame($ownerMock, $content->owner);
    }

    public function testContentOwnerDoesNotExist()
    {
        $content = $this->getMockedContent();

        $this
            ->loadServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with('ownerId')
            ->willThrowException(
                new NotFoundException('ContentType', 'contentTypeId')
            );

        $this->assertNull($content->owner);
    }

    public function testContentOwnerDoesNotExistRepeated()
    {
        $content = $this->getMockedContent();

        $this
            ->loadServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with('ownerId')
            ->willThrowException(
                new NotFoundException('ContentType', 'contentTypeId')
            );

        $this->assertNull($content->owner);
        $this->assertNull($content->owner);
    }

    public function testContentInnerOwnerUserExists()
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

    public function testContentInnerOwnerUserExistsRepeated()
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

    public function testContentInnerOwnerUserDoesNotExist()
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

    public function testContentInnerOwnerUserDoesNotExistRepeated()
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
    protected function getMockedContent()
    {
        return new Content([
            'site' => $this->getSiteMock(),
            'domainObjectMapper' => $this->getDomainObjectMapper(),
            'contentService' => $this->getContentServiceMock(),
            'userService' => $this->getUserServiceMock(),
            'innerVersionInfo' => new VersionInfo([
                'contentInfo' => new RepoContentInfo([
                    'ownerId' => 'ownerId',
                    'contentTypeId' => 'contentTypeId',
                ])
            ]),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSiteMock()
    {
        if (null !== $this->siteMock) {
            return $this->siteMock;
        }

        $this->siteMock = $this
            ->getMockBuilder(Site::class)
            ->getMock();

        $this->siteMock
            ->expects($this->any())
            ->method('getLoadService')
            ->willReturn($this->getLoadServiceMock());

        return $this->siteMock;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    protected function getDomainObjectMapper()
    {
        if (null !== $this->domainObjectMapper) {
            return $this->domainObjectMapper;
        }

        $this->domainObjectMapper = new DomainObjectMapper(
            $this->getSiteMock(),
            $this->getContentServiceMock(),
            $this->getContentTypeServiceMock(),
            $this->getFieldTypeServiceMock(),
            $this->getUserServiceMock()
        );

        return $this->domainObjectMapper;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Netgen\EzPlatformSiteApi\API\LoadService
     */
    protected function getLoadServiceMock()
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentService
     */
    protected function getContentServiceMock()
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\ContentTypeService
     */
    protected function getContentTypeServiceMock()
    {
        if (null !== $this->contentTypeServiceMock) {
            return $this->contentTypeServiceMock;
        }

        $this->contentTypeServiceMock = $this
            ->getMockBuilder(ContentTypeService::class)
            ->getMock();

        $this->contentTypeServiceMock
            ->expects($this->any())
            ->method('loadContentType')
            ->with('contentTypeId')
            ->willReturn(new ContentType());

        return $this->contentTypeServiceMock;
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

        return $this->fieldTypeServiceMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\eZ\Publish\API\Repository\UserService
     */
    protected function getUserServiceMock()
    {
        if (null !== $this->userServiceMock) {
            return $this->userServiceMock;
        }

        $this->userServiceMock = $this
            ->getMockBuilder(UserService::class)
            ->getMock();

        return $this->userServiceMock;
    }
}
