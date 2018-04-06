<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

/**
 * Test case for the FilterService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FilterService
 *
 * @group integration
 * @group filter
 */
class FilterServiceTest extends FilterServiceBaseTest
{
    /**
     * Test for the filterContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentMatchPrimaryLanguage($data)
    {
        $this->doTestFilterContent($data);
    }

    /**
     * Test for the filterContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFilterContent($data);
    }

    /**
     * Test for the filterContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFilterContent($data);
    }

    /**
     * Test for the filterContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentTranslationNotMatched($data)
    {
        $this->doTestFilterContent($data);
    }

    /**
     * Test for the filterContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentInfoMatchPrimaryLanguage($data)
    {
        $this->doTestFilterContentInfo($data);
    }

    /**
     * Test for the filterContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentInfoMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFilterContentInfo($data);
    }

    /**
     * Test for the filterContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentInfoMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFilterContentInfo($data);
    }

    /**
     * Test for the filterContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterContentInfoTranslationNotMatched($data)
    {
        $this->doTestFilterContentInfo($data);
    }

    /**
     * Test for the filterLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getPrimaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterLocationsMatchPrimaryLanguage($data)
    {
        $this->doTestFilterLocations($data);
    }

    /**
     * Test for the filterLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getSecondaryLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterLocationsMatchSecondaryLanguage($data)
    {
        $this->createSecondaryTranslationFallback();

        $this->doTestFilterLocations($data);
    }

    /**
     * Test for the filterLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getAlwaysAvailableLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterLocationsMatchAlwaysAvailableLanguage($data)
    {
        $this->doTestFilterLocations($data);
    }

    /**
     * Test for the filterLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FilterService::filterLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     * @dataProvider getNoLanguageMatchData
     *
     * @param mixed $data
     */
    public function testFilterLocationsTranslationNotMatched($data)
    {
        $this->doTestFilterLocations($data);
    }
}
