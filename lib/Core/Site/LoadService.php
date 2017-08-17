<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use Netgen\EzPlatformSiteApi\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSiteApi\API\Settings as BaseSettings;
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

    public function loadContent($contentId, $versionNo = null, $languageCode = null)
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

    public function loadContentByRemoteId($remoteId)
    {
        $contentInfo = $this->loadContentInfoByRemoteId($remoteId);

        return $this->loadContent($contentInfo->id);
    }

    public function loadContentInfo($contentId, $versionNo = null, $languageCode = null)
    {
        @trigger_error('loadContentInfo() is deprecated since version 2.2 and will be removed in 3.0. Use loadContent() instead.', E_USER_DEPRECATED);

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

        return $this->domainObjectMapper->mapContentInfo($versionInfo, $languageCode);
    }

    public function loadContentInfoByRemoteId($remoteId)
    {
        @trigger_error('loadContentInfoByRemoteId() is deprecated since version 2.2 and will be removed in 3.0. Use loadContentByRemoteId() instead.', E_USER_DEPRECATED);

        $contentInfo = $this->contentService->loadContentInfoByRemoteId($remoteId);

        return $this->loadContentInfo($contentInfo->id);
    }

    public function loadLocation($locationId)
    {
        $location = $this->locationService->loadLocation($locationId);

        return $this->getSiteLocation($location);
    }

    public function loadLocationByRemoteId($remoteId)
    {
        $location = $this->locationService->loadLocationByRemoteId($remoteId);

        return $this->getSiteLocation($location);
    }

    public function loadNode($locationId)
    {
        @trigger_error('loadNode() is deprecated since version 2.1 and will be removed in 3.0. Use loadLocation() instead.', E_USER_DEPRECATED);

        $location = $this->locationService->loadLocation($locationId);

        return $this->getSiteNode($location);
    }

    public function loadNodeByRemoteId($remoteId)
    {
        @trigger_error('loadNodeByRemoteId() is deprecated since version 2.1 and will be removed in 3.0. Use loadLocationByRemoteId() instead.', E_USER_DEPRECATED);

        $location = $this->locationService->loadLocationByRemoteId($remoteId);

        return $this->getSiteNode($location);
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
    private function getSiteLocation(APILocation $location)
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
     * Returns Site Node object for the given Repository $location.
     *
     * @throws \Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Node
     */
    private function getSiteNode(APILocation $location)
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

        return $this->domainObjectMapper->mapNode(
            $location,
            $this->contentService->loadContent(
                $location->contentInfo->id,
                [$languageCode],
                $location->contentInfo->currentVersionNo
            ),
            $languageCode
        );
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
    private function getLanguage(array $languageCodes, $mainLanguageCode, $alwaysAvailable)
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
    private function getContext(VersionInfo $versionInfo)
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
