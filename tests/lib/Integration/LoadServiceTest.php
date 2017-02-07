<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

/**
 * Test case for the LoadService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\LoadService
 *
 * @group integration
 * @group load
 */
class LoadServiceTest extends LoadServiceBaseTest
{
    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentMatchPrimaryLanguage($data)
    {
        $this->doTestLoadContent($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadContent($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadContent($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getExplicitVersionAndLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInExplicitVersionAndLanguage($data)
    {
        $this->doTestLoadContentInExplicitVersionAndLanguage($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContent($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInLanguageThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContentInLanguage($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentByRemoteIdMatchPrimaryLanguage($data)
    {
        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentByRemoteIdMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentByRemoteIdMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentByRemoteIdThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoMatchPrimaryLanguage($data)
    {
        $this->doTestLoadContentInfo($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadContentInfo($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadContentInfo($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getExplicitVersionAndLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoInExplicitVersionAndLanguage($data)
    {
        $this->doTestLoadContentInfoInExplicitVersionAndLanguage($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContentInfo($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoMissingLanguageThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContentInfoInLanguage($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoByRemoteIdMatchPrimaryLanguage($data)
    {
        $this->doTestLoadContentInfoByRemoteId($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoByRemoteIdMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadContentInfoByRemoteId($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoByRemoteIdMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadContentInfoByRemoteIdThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadContentByRemoteId($data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocation()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationMatchPrimaryLanguage($data)
    {
        $this->doTestLoadLocation($data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocation()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadLocation($data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocation()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadLocation($data);
    }

    /**
     * Test for the loadLocation() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocation()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadLocation($data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocationByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationByRemoteIdMatchPrimaryLanguage($data)
    {
        $this->doTestLoadLocationByRemoteId($data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocationByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationByRemoteIdMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadLocationByRemoteId($data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocationByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationByRemoteIdMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadLocationByRemoteId($data);
    }

    /**
     * Test for the loadLocationByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadLocationByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadLocationByRemoteIdThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadLocationByRemoteId($data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNode()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeMatchPrimaryLanguage($data)
    {
        $this->doTestLoadNode($data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNode()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadNode($data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNode()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadNode($data);
    }

    /**
     * Test for the loadNode() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNode()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadNode($data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNodeByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeByRemoteIdMatchPrimaryLanguage($data)
    {
        $this->doTestLoadNodeByRemoteId($data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNodeByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeByRemoteIdMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestLoadNodeByRemoteId($data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNodeByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeByRemoteIdMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestLoadNodeByRemoteId($data);
    }

    /**
     * Test for the loadNodeByRemoteId() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\LoadService::loadNodeByRemoteId()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetLoadService
     * @expectedException \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testLoadNodeByRemoteIdThrowsTranslationNotMatchedException($data)
    {
        $this->doTestLoadNodeByRemoteId($data);
    }
}
