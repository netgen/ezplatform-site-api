<?php

namespace Netgen\EzPlatformSite\Tests\Integration;

/**
 * Test case for the FindService.
 *
 * @see \Netgen\EzPlatformSite\API\FindService
 *
 * @group integration
 * @group find
 */
class FindServiceTest extends FindServiceBaseTest
{
    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContent()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentMatchPrimaryLanguage($data)
    {
        $this->doTestFindContent($data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContent()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFindContent($data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContent()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFindContent($data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContent()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentTranslationNotMatched($data)
    {
        $this->doTestFindContent($data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentInfoMatchPrimaryLanguage($data)
    {
        $this->doTestFindContentInfo($data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentInfoMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFindContentInfo($data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentInfoMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFindContentInfo($data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindContentInfoTranslationNotMatched($data)
    {
        $this->doTestFindContentInfo($data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindLocationsMatchPrimaryLanguage($data)
    {
        $this->doTestFindLocations($data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindLocationsMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFindLocations($data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindLocationsMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFindLocations($data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindLocationsTranslationNotMatched($data)
    {
        $this->doTestFindLocations($data);
    }

    /**
     * Test for the findNodes() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findNodes()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindNodesMatchPrimaryLanguage($data)
    {
        $this->doTestFindNodes($data);
    }

    /**
     * Test for the findNodes() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findNodes()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindNodesMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFindNodes($data);
    }

    /**
     * Test for the findNodes() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findNodes()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindNodesNodesMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFindNodes($data);
    }

    /**
     * Test for the findNodes() method.
     *
     * @see \Netgen\EzPlatformSite\API\FindService::findNodes()
     * @depends Netgen\EzPlatformSite\Tests\Integration\SiteTest::testGetFindService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFindNodesTranslationNotMatched($data)
    {
        $this->doTestFindNodes($data);
    }
}
