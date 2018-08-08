<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException;

/**
 * Test case for the LoadService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\LoadService
 *
 * @group integration
 * @group load
 */
class LoadServiceTest extends BaseTest
{
    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContent($data['contentId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContent($data['contentId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContent($data['contentId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInExplicitVersionAndLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContent($data['contentId'], 1, 'ger-DE');

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContent(52);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInLanguageThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $data = $this->getData('klingon');
        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContent($data['contentId'], null, 'klingon');
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentByRemoteIdMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentByRemoteId($data['contentRemoteId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentByRemoteIdMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContentByRemoteId($data['contentRemoteId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentByRemoteIdMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentByRemoteId($data['contentRemoteId']);

        $this->assertContent($content, $data);
    }

    /**
     * Test for the loadContentByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentByRemoteIdThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContentByRemoteId('27437f3547db19cf81a33c92578b2c89');
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentInfo($data['contentId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContentInfo($data['contentId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentInfo($data['contentId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoInExplicitVersionAndLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContentInfo($data['contentId'], 1, 'ger-DE');

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContentInfo(52);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoInLanguageThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $data = $this->getData('klingon');
        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContentInfo($data['contentId'], null, 'klingon');
    }

    /**
     * Test for the loadContentInfoByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoByRemoteIdMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentInfoByRemoteId($data['contentRemoteId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfoByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoByRemoteIdMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $content = $loadService->loadContentInfoByRemoteId($data['contentRemoteId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfoByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoByRemoteIdMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $content = $loadService->loadContentInfoByRemoteId($data['contentRemoteId']);

        $this->assertContentInfo($content, $data);
    }

    /**
     * Test for the loadContentInfoByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadContentInfoByRemoteIdThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadContentInfoByRemoteId('27437f3547db19cf81a33c92578b2c89');
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $location = $loadService->loadLocation($data['locationId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $location = $loadService->loadLocation($data['locationId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $location = $loadService->loadLocation($data['locationId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadLocation(54);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationByRemoteIdMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $location = $loadService->loadLocationByRemoteId($data['locationRemoteId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationByRemoteIdMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $location = $loadService->loadLocationByRemoteId($data['locationRemoteId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationByRemoteIdMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $location = $loadService->loadLocationByRemoteId($data['locationRemoteId']);

        $this->assertLocation($location, $data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadLocationByRemoteIdThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadLocationByRemoteId('fa9f3cff9cf90ecfae335718dcbddfe2');
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $node = $loadService->loadNode($data['locationId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $node = $loadService->loadNode($data['locationId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $node = $loadService->loadNode($data['locationId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadNode(54);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeByRemoteIdMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $node = $loadService->loadNodeByRemoteId($data['locationRemoteId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeByRemoteIdMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('ger-DE');
        $node = $loadService->loadNodeByRemoteId($data['locationRemoteId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeByRemoteIdMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $loadService = $this->getSite()->getLoadService();

        $data = $this->getData('eng-GB');
        $node = $loadService->loadNodeByRemoteId($data['locationRemoteId']);

        $this->assertLocation($node, $data);
        $this->assertContent($node->content, $data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testLoadNodeByRemoteIdThrowsTranslationNotMatchedException()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $this->expectException(TranslationNotMatchedException::class);

        $loadService = $this->getSite()->getLoadService();

        $loadService->loadNodeByRemoteId('fa9f3cff9cf90ecfae335718dcbddfe2');
    }
}
