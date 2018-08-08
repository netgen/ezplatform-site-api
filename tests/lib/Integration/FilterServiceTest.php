<?php

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Test case for the FilterService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FilterService
 *
 * @group integration
 * @group filter
 */
class FilterServiceTest extends BaseTest
{
    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterContent(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('ger-DE');
        $searchResult = $filterService->filterContent(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterContent(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentTranslationNotMatched()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $searchResult = $filterService->filterContent(
            new Query([
                'filter' => new ContentId(52),
            ])
        );

        $this->assertEquals(0, $searchResult->totalCount);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentInfoMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterContentInfo(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentInfoSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentInfoMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('ger-DE');
        $searchResult = $filterService->filterContentInfo(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentInfoSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentInfoMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterContentInfo(
            new Query([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertContentInfoSearchResult($searchResult, $data);
    }

    /**
     * Test for the findContentInfo() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContentInfo()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterContentInfoTranslationNotMatched()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $searchResult = $filterService->filterContentInfo(
            new Query([
                'filter' => new ContentId(52),
            ])
        );

        $this->assertEquals(0, $searchResult->totalCount);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterLocationsMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterLocations(
            new LocationQuery([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertLocationSearchResult($searchResult, $data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterLocationsMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('ger-DE');
        $searchResult = $filterService->filterLocations(
            new LocationQuery([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertLocationSearchResult($searchResult, $data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterLocationsMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $data = $this->getData('eng-GB');
        $searchResult = $filterService->filterLocations(
            new LocationQuery([
                'filter' => new ContentId($data['contentId']),
            ])
        );

        $this->assertLocationSearchResult($searchResult, $data);
    }

    /**
     * Test for the findLocations() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findLocations()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFilterService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFilterLocationsTranslationNotMatched()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $filterService = $this->getSite()->getFilterService();

        $searchResult = $filterService->filterLocations(
            new LocationQuery([
                'filter' => new ContentId(52),
            ])
        );

        $this->assertEquals(0, $searchResult->totalCount);
    }

    protected function assertContentSearchResult(SearchResult $searchResult, $data)
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContent($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertContentInfoSearchResult(SearchResult $searchResult, $data)
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContentInfo($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertLocationSearchResult(SearchResult $searchResult, $data)
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertLocation($searchResult->searchHits[0]->valueObject, $data);
    }
}
