<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Netgen\EzPlatformSiteApi\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSettings;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException;

/**
 * @final
 *
 * @internal
 *
 * Hint against API interface instead of this service:
 *
 * @see \Netgen\EzPlatformSiteApi\API\LoadService
 */
class LoadService implements LoadServiceInterface
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Settings
     */
    private $settings;

    /**
     * @var \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper
     */
    private $domainObjectMapper;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    public function __construct(
        BaseSettings $settings,
        DomainObjectMapper $domainObjectMapper,
        ContentService $contentService,
        LocationService $locationService
    ) {
        $this->settings = $settings;
        $this->domainObjectMapper = $domainObjectMapper;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    public function loadContent($contentId, ?int $versionNo = null, ?string $languageCode = null): Content
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNo);
        $languageCode = $this->resolveLanguageCode($versionInfo, $languageCode);

        return $this->domainObjectMapper->mapContent($versionInfo, $languageCode);
    }

    public function loadContentByRemoteId(string $remoteId): Content
    {
        $contentInfo = $this->contentService->loadContentInfoByRemoteId($remoteId);

        return $this->loadContent($contentInfo->id);
    }

    public function loadLocation($locationId): Location
    {
        $location = $this->locationService->loadLocation($locationId);

        return $this->getSiteLocation($location);
    }

    public function loadLocationByRemoteId(string $remoteId): Location
    {
        $location = $this->locationService->loadLocationByRemoteId($remoteId);

        return $this->getSiteLocation($location);
    }

    /**
     * Returns Site Location object for the given Repository $location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getSiteLocation(APILocation $location): Location
    {
        $versionInfo = $this->contentService->loadVersionInfoById($location->contentInfo->id);
        $languageCode = $this->resolveLanguageCode($versionInfo);

        return $this->domainObjectMapper->mapLocation($location, $versionInfo, $languageCode);
    }

    /**
     * Returns the most prioritized language code for the given parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param null|string $languageCode
     *
     * @throws \Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException
     *
     * @return string
     */
    private function resolveLanguageCode(VersionInfo $versionInfo, ?string $languageCode = null): string
    {
        if ($languageCode === null) {
            return $this->resolveLanguageCodeFromConfiguration($versionInfo);
        }

        if (!\in_array($languageCode, $versionInfo->languageCodes, true)) {
            throw new TranslationNotMatchedException($versionInfo->contentInfo->id, $this->getContext($versionInfo));
        }

        return $languageCode;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @throws \Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException
     *
     * @return string
     */
    private function resolveLanguageCodeFromConfiguration(VersionInfo $versionInfo): string
    {
        foreach ($this->settings->prioritizedLanguages as $languageCode) {
            if (\in_array($languageCode, $versionInfo->languageCodes, true)) {
                return $languageCode;
            }
        }

        if ($this->settings->useAlwaysAvailable && $versionInfo->contentInfo->alwaysAvailable) {
            return $versionInfo->contentInfo->mainLanguageCode;
        }

        throw new TranslationNotMatchedException($versionInfo->contentInfo->id, $this->getContext($versionInfo));
    }

    /**
     * Returns an array describing language resolving context.
     *
     * To be used when throwing TranslationNotMatchedException.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     *
     * @return array
     */
    private function getContext(VersionInfo $versionInfo): array
    {
        return [
            'prioritizedLanguages' => $this->settings->prioritizedLanguages,
            'useAlwaysAvailable' => $this->settings->useAlwaysAvailable,
            'availableTranslations' => $versionInfo->languageCodes,
            'mainTranslation' => $versionInfo->contentInfo->mainLanguageCode,
            'alwaysAvailable' => $versionInfo->contentInfo->alwaysAvailable,
        ];
    }
}
