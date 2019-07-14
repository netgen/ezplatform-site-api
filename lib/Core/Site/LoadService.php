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

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Settings $settings
     * @param \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper $domainObjectMapper
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
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

    public function loadContent($contentId, $versionNo = null, $languageCode = null): Content
    {
        $versionInfo = $this->contentService->loadVersionInfoById($contentId, $versionNo);

        if ($languageCode === null) {
            $languageCode = $this->getLanguage(
                $versionInfo->languageCodes,
                $versionInfo->contentInfo->mainLanguageCode,
                $versionInfo->contentInfo->alwaysAvailable
            );

            if ($languageCode === null) {
                throw new TranslationNotMatchedException($contentId, $this->getContext($versionInfo));
            }
        } elseif (!in_array($languageCode, $versionInfo->languageCodes)) {
            throw new TranslationNotMatchedException($contentId, $this->getContext($versionInfo));
        }

        return $this->domainObjectMapper->mapContent($versionInfo, $languageCode);
    }

    public function loadContentByRemoteId($remoteId): Content
    {
        $contentInfo = $this->contentService->loadContentInfoByRemoteId($remoteId);

        return $this->loadContent($contentInfo->id);
    }

    public function loadLocation($locationId): Location
    {
        $location = $this->locationService->loadLocation($locationId);

        return $this->getSiteLocation($location);
    }

    public function loadLocationByRemoteId($remoteId): Location
    {
        $location = $this->locationService->loadLocationByRemoteId($remoteId);

        return $this->getSiteLocation($location);
    }

    /**
     * Returns Site Location object for the given Repository $location.
     *
     * @throws \Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    private function getSiteLocation(APILocation $location): Location
    {
        $versionInfo = $this->contentService->loadVersionInfoById($location->contentInfo->id);

        $languageCode = $this->getLanguage(
            $versionInfo->languageCodes,
            $versionInfo->contentInfo->mainLanguageCode,
            $versionInfo->contentInfo->alwaysAvailable
        );

        if ($languageCode === null) {
            throw new TranslationNotMatchedException(
                $versionInfo->contentInfo->id,
                $this->getContext($versionInfo)
            );
        }

        return $this->domainObjectMapper->mapLocation($location, $versionInfo, $languageCode);
    }

    /**
     * Returns the most prioritized language for the given parameters.
     *
     * Will return null if language could not be resolved.
     *
     * @param string[] $languageCodes
     * @param string $mainLanguageCode
     * @param bool $alwaysAvailable
     *
     * @return string|null
     */
    private function getLanguage(array $languageCodes, $mainLanguageCode, $alwaysAvailable): ?string
    {
        $languageCodesSet = array_flip($languageCodes);

        foreach ($this->settings->prioritizedLanguages as $languageCode) {
            if (isset($languageCodesSet[$languageCode])) {
                return $languageCode;
            }
        }

        if ($this->settings->useAlwaysAvailable && $alwaysAvailable) {
            return $mainLanguageCode;
        }

        return null;
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
