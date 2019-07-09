<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Tests\Integration;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;

/**
 * Test case for the FindService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FindService
 *
 * @group integration
 * @group find
 */
class FindServiceTest extends BaseTest
{
    /**
     * Test for the findContent() method.
     *
     * @see \Netgen\EzPlatformSiteApi\API\FindService::findContent()
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\PrepareFixturesTest::testPrepareTestFixtures
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindContentMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('eng-GB');
        $searchResult = $findService->findContent(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindContentMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('ger-DE');
        $searchResult = $findService->findContent(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindContentMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('eng-GB');
        $searchResult = $findService->findContent(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindContentTranslationNotMatched()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $searchResult = $findService->findContent(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindLocationsMatchPrimaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('eng-GB');
        $searchResult = $findService->findLocations(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindLocationsMatchSecondaryLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('ger-DE');
        $searchResult = $findService->findLocations(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindLocationsMatchAlwaysAvailableLanguage()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-US',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $data = $this->getData('eng-GB');
        $searchResult = $findService->findLocations(
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
     * @depends Netgen\EzPlatformSiteApi\Tests\Integration\SiteTest::testGetFindService
     *
     * @throws \ReflectionException
     * @throws \ErrorException
     */
    public function testFindLocationsTranslationNotMatched()
    {
        $this->overrideSettings(
            'prioritizedLanguages',
            [
                'eng-GB',
                'ger-DE',
            ]
        );

        $findService = $this->getSite()->getFindService();

        $searchResult = $findService->findLocations(
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

    protected function assertLocationSearchResult(SearchResult $searchResult, $data)
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertLocation($searchResult->searchHits[0]->valueObject, $data);
    }
}
