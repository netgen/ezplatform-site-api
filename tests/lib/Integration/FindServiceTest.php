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
 *
 * @internal
 */
final class FindServiceTest extends BaseTest
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
    public function testFindContentMatchPrimaryLanguage(): void
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
    public function testFindContentMatchSecondaryLanguage(): void
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
    public function testFindContentMatchAlwaysAvailableLanguage(): void
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
    public function testFindContentTranslationNotMatched(): void
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
    public function testFindLocationsMatchPrimaryLanguage(): void
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
    public function testFindLocationsMatchSecondaryLanguage(): void
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
    public function testFindLocationsMatchAlwaysAvailableLanguage(): void
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
    public function testFindLocationsTranslationNotMatched(): void
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

    protected function assertContentSearchResult(SearchResult $searchResult, $data): void
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertContent($searchResult->searchHits[0]->valueObject, $data);
    }

    protected function assertLocationSearchResult(SearchResult $searchResult, $data): void
    {
        $languageCode = $data['languageCode'];

        $this->assertSame(1, $searchResult->totalCount);
        $this->assertSame($languageCode, $searchResult->searchHits[0]->matchedTranslation);
        $this->assertLocation($searchResult->searchHits[0]->valueObject, $data);
    }
}
