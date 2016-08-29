<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use Netgen\EzPlatformSiteApi\API\LoadService as LoadServiceInterface;
use Netgen\EzPlatformSiteApi\Core\Site\Exceptions\TranslationNotMatchedException;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;

final class LoadService implements LoadServiceInterface
{
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
     * @var string[]
     */
    private $prioritizedLanguages;

    /**
     * @var bool
     */
    private $useAlwaysAvailable;

    /**
     * @param \Netgen\EzPlatformSiteApi\Core\Site\DomainObjectMapper $domainObjectMapper
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param string[] $prioritizedLanguages
     * @param bool $useAlwaysAvailable
     */
    public function __construct(
        DomainObjectMapper $domainObjectMapper,
        ContentService $contentService,
        LocationService $locationService,
        array $prioritizedLanguages,
        $useAlwaysAvailable
    ) {
        $this->domainObjectMapper = $domainObjectMapper;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->prioritizedLanguages = $prioritizedLanguages;
        $this->useAlwaysAvailable = $useAlwaysAvailable;
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

        $content = $this->contentService->loadContent($contentId, [$languageCode], $versionNo);

        return $this->domainObjectMapper->mapContent($content, $languageCode);
    }

    public function loadContentByRemoteId($remoteId)
    {
        $contentInfo = $this->loadContentInfoByRemoteId($remoteId);

        return $this->loadContent($contentInfo->id);
    }

    public function loadContentInfo($contentId, $versionNo = null, $languageCode = null)
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

        return $this->domainObjectMapper->mapContentInfo($versionInfo, $languageCode);
    }

    public function loadContentInfoByRemoteId($remoteId)
    {
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
        $location = $this->locationService->loadLocation($locationId);

        return $this->getSiteNode($location);
    }

    public function loadNodeByRemoteId($remoteId)
    {
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
     * @param bool $useAlwaysAvailable
     *
     * @return string|null
     */
    private function getLanguage(array $languageCodes, $mainLanguageCode, $useAlwaysAvailable)
    {
        $languageCodesSet = array_flip($languageCodes);

        foreach ($this->prioritizedLanguages as $languageCode) {
            if (isset($languageCodesSet[$languageCode])) {
                return $languageCode;
            }
        }

        if ($this->useAlwaysAvailable && $useAlwaysAvailable) {
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
            'prioritizedLanguages' => $this->prioritizedLanguages,
            'useAlwaysAvailable' => $this->useAlwaysAvailable,
            'availableTranslations' => $versionInfo->languageCodes,
            'mainTranslation' => $versionInfo->contentInfo->mainLanguageCode,
            'alwaysAvailable' => $versionInfo->contentInfo->alwaysAvailable,
        ];
    }
}
