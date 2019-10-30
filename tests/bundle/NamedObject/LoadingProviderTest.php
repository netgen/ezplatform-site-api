<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Tests\NamedObject;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider\Loading;
use Netgen\EzPlatformSiteApi\API\LoadService;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
final class LoadingProviderTest extends TestCase
{
    protected $providerUnderTest;

    public function testHasContentReturnsTrue(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertTrue($provider->hasContent('apple'));
    }

    public function testHasContentReturnsFalse(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertFalse($provider->hasContent('quince'));
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetContent(): void
    {
        $provider = $this->getProviderUnderTest();

        $content = $provider->getContent('apple');

        $this->assertSame($this->getContentMock(), $content);
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetContentThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('content not found');

        $provider->getContent('plum');
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetContentThroughRemoteId(): void
    {
        $provider = $this->getProviderUnderTest();

        $content = $provider->getContent('pear');

        $this->assertSame($this->getContentMock(), $content);
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetContentThroughRemoteIdThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('content not found');

        $provider->getContent('fig');
    }

    public function testHasLocationReturnsTrue(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertTrue($provider->hasLocation('apple'));
    }

    public function testHasLocationReturnsFalse(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertFalse($provider->hasLocation('quince'));
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetLocation(): void
    {
        $provider = $this->getProviderUnderTest();

        $location = $provider->getLocation('apple');

        $this->assertSame($this->getLocationMock(), $location);
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetLocationThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('location not found');

        $provider->getLocation('plum');
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetLocationThroughRemoteId(): void
    {
        $provider = $this->getProviderUnderTest();

        $location = $provider->getLocation('pear');

        $this->assertSame($this->getLocationMock(), $location);
    }

    /**
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetLocationThroughRemoteIdThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('location not found');

        $provider->getLocation('fig');
    }

    public function testHasTagReturnsTrue(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertTrue($provider->hasContent('apple'));
    }

    public function testHasTagReturnsFalse(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->assertFalse($provider->hasContent('quince'));
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetTag(): void
    {
        $provider = $this->getProviderUnderTest();

        $tag = $provider->getTag('apple');

        $this->assertSame($this->getTagMock(), $tag);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetTagThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('tag not found');

        $provider->getTag('plum');
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetTagThroughRemoteId(): void
    {
        $provider = $this->getProviderUnderTest();

        $tag = $provider->getTag('pear');

        $this->assertSame($this->getTagMock(), $tag);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testGetTagThroughRemoteIdThrowsException(): void
    {
        $provider = $this->getProviderUnderTest();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('tag not found');

        $provider->getTag('fig');
    }

    protected function getProviderUnderTest(): Loading
    {
        if ($this->providerUnderTest === null) {
            $this->providerUnderTest = new Loading(
                $this->getLoadServiceMock(),
                $this->getTagServiceMock(),
                $this->getConfigResolverMock()
            );
        }

        return $this->providerUnderTest;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\LoadService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getLoadServiceMock(): MockObject
    {
        $mock = $this->getMockBuilder(LoadService::class)->getMock();

        $mock
            ->method('loadContent')
            ->willReturnCallback(function ($id) {
                if ($id === 42) {
                    return $this->getContentMock();
                }

                throw new RuntimeException('content not found');
            });

        $mock
            ->method('loadContentByRemoteId')
            ->willReturnCallback(function ($id) {
                if ($id === 'abc') {
                    return $this->getContentMock();
                }

                throw new RuntimeException('content not found');
            });

        $mock
            ->method('loadLocation')
            ->willReturnCallback(function ($id) {
                if ($id === 42) {
                    return $this->getLocationMock();
                }

                throw new RuntimeException('location not found');
            });

        $mock
            ->method('loadLocationByRemoteId')
            ->willReturnCallback(function ($id) {
                if ($id === 'abc') {
                    return $this->getLocationMock();
                }

                throw new RuntimeException('location not found');
            });

        return $mock;
    }

    /**
     * @return \Netgen\TagsBundle\API\Repository\TagsService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getTagServiceMock(): MockObject
    {
        $mock = $this->getMockBuilder(TagsService::class)->getMock();

        $mock
            ->method('loadTag')
            ->willReturnCallback(function ($id) {
                if ($id === 42) {
                    return $this->getTagMock();
                }

                throw new RuntimeException('tag not found');
            });

        $mock
            ->method('loadTagByRemoteId')
            ->willReturnCallback(function ($id) {
                if ($id === 'abc') {
                    return $this->getTagMock();
                }

                throw new RuntimeException('tag not found');
            });

        return $mock;
    }

    /**
     * @return \eZ\Publish\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getConfigResolverMock(): MockObject
    {
        $configResolverMock = $this->getMockBuilder(ConfigResolverInterface::class)->getMock();

        $getParameterMap = [
            [
                'named_objects',
                'netgen_ez_platform_site_api',
                null,
                [
                    'content' => $template = [
                        'apple' => [
                            'id' => 42,
                        ],
                        'pear' => [
                            'remote_id' => 'abc',
                        ],
                        'plum' => [
                            'id' => 24,
                        ],
                        'fig' => [
                            'remote_id' => 'cba',
                        ],
                    ],
                    'location' => $template,
                    'tag' => $template,
                ],
            ],
        ];

        $configResolverMock
            ->method('getParameter')
            ->willReturnMap($getParameterMap);

        return $configResolverMock;
    }

    protected function getContentMock(): MockObject
    {
        static $contentMock;

        if ($contentMock === null) {
            $contentMock = $this->getMockBuilder(Content::class)->getMock();
        }

        return $contentMock;
    }

    protected function getLocationMock(): MockObject
    {
        static $locationMock;

        if ($locationMock === null) {
            $locationMock = $this->getMockBuilder(Location::class)->getMock();
        }

        return $locationMock;
    }

    protected function getTagMock(): MockObject
    {
        static $tagMock;

        if ($tagMock === null) {
            $tagMock = $this->getMockBuilder(Tag::class)->getMock();
        }

        return $tagMock;
    }
}
