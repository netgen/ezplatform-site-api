<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use Exception;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use eZ\Publish\SPI\Persistence\Handler;
use function array_fill_keys;
use function array_key_exists;
use function array_map;
use function in_array;
use function reset;

/**
 * @internal do not depend on this service, it can be changed without warning
 */
class SiteaccessResolver
{
    private $persistenceHandler;
    private $excludedSiteaccessSet;
    private $excludedSiteaccessGroupSet;

    /**
     * @var ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var SiteAccess
     */
    private $currentSiteaccess;

    /**
     * @var array
     */
    private $siteaccesses;

    /**
     * @var array
     */
    private $siteaccessGroupsBySiteaccess;

    /**
     * @var array
     */
    private $siteaccessRootLocationIdMap;

    /**
     * @var array
     */
    private $locationIdSiteaccessSetMap = [];

    public function __construct(
        Handler $persistenceHandler,
        array $excludedSiteaccesses,
        array $excludedSiteaccessGroups
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->excludedSiteaccessSet = array_fill_keys($excludedSiteaccesses, true);
        $this->excludedSiteaccessGroupSet = array_fill_keys($excludedSiteaccessGroups, true);
    }

    /**
     * @param ConfigResolverInterface $configResolver
     */
    public function setConfigResolver(ConfigResolverInterface $configResolver): void
    {
        $this->configResolver = $configResolver;
    }

    public function setSiteaccess(SiteAccess $currentSiteAccess = null): void
    {
        $this->currentSiteaccess = $currentSiteAccess;
    }

    public function setSiteaccessList(array $siteaccesses): void
    {
        $this->siteaccesses = $siteaccesses;
    }

    public function setSiteaccessGroupsBySiteaccess(array $siteaccessGroupsBySiteaccess): void
    {
        $this->siteaccessGroupsBySiteaccess = $siteaccessGroupsBySiteaccess;
    }

    public function resolve(Location $location): string
    {
        $siteaccess = $this->selectSiteaccess($location);
        $translationSiteaccesses = $this->getTranslationSiteaccesses($siteaccess);

        if (empty($translationSiteaccesses)) {
            return $siteaccess;
        }

        try {
            $availableLanguageCodeSet = $this->getLanguageCodeSet($location);
        } catch (Exception $e) {
            return $siteaccess;
        }


        if ($this->isCurrentSiteaccessAndLanguageIsAllowed($siteaccess, $availableLanguageCodeSet)) {
            return $siteaccess;
        }

        foreach ($translationSiteaccesses as $translationSiteaccess) {
            if ($this->isSiteaccessExcluded($translationSiteaccess)) {
                continue;
            }

            $topLanguageCode = $this->getTopLanguageCode($translationSiteaccess);

            if (array_key_exists($topLanguageCode, $availableLanguageCodeSet)) {
                return $translationSiteaccess;
            }
        }

        return $siteaccess;
    }

    private function isCurrentSiteaccessAndLanguageIsAllowed(string $siteaccess, array $availableLanguageCodeSet): bool
    {
        if ($siteaccess !== $this->currentSiteaccess->name) {
            return false;
        }

        $currentPrioritizedLanguageCodes = $this->getCurrentPrioritizedLanguageCodes();

        foreach ($currentPrioritizedLanguageCodes as $languageCode) {
            if (array_key_exists($languageCode, $availableLanguageCodeSet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Exception
     *
     * @return string[]
     */
    private function getLanguageCodeSet(Location $location): array
    {
        $languageCodes = $this->persistenceHandler->contentHandler()->loadVersionInfo($location->contentId)->languageCodes;

        return array_fill_keys($languageCodes, true);
    }

    private function selectSiteaccess(Location $location): string
    {
        $set = $this->getSiteaccessSet($location);

        if (empty($set) || array_key_exists($this->currentSiteaccess->name, $set)) {
            return $this->currentSiteaccess->name;
        }

        return array_key_first($set);
    }

    /**
     * @return string
     */
    private function getTopLanguageCode(string $siteaccess): string
    {
        $prioritizedLanguageCodes = $this->getPrioritizedLanguageCodes($siteaccess);

        return reset($prioritizedLanguageCodes);
    }

    /**
     * @return string[]
     */
    private function getPrioritizedLanguageCodes(string $siteaccess): array
    {
        return $this->configResolver->getParameter('languages', null, $siteaccess);
    }

    /**
     * @return string[]
     */
    private function getCurrentPrioritizedLanguageCodes(): array
    {
        return $this->getPrioritizedLanguageCodes($this->currentSiteaccess->name);
    }

    /**
     * @return string[]
     */
    private function getTranslationSiteaccesses(string $siteaccess): array
    {
        if ($this->configResolver->hasParameter('translation_siteaccesses', null, $siteaccess)) {
            return $this->configResolver->getParameter(
                'translation_siteaccesses',
                null,
                $siteaccess
            );
        }

        return [];
    }

    private function getSiteaccessSet(Location $location): array
    {
        if (array_key_exists($location->id, $this->locationIdSiteaccessSetMap)) {
            return $this->locationIdSiteaccessSetMap[$location->id];
        }

        $ancestorAndSelfLocationIds = array_map('\intval', $location->path);
        $this->initializeSiteaccessRootLocationIdMap();
        $set = [];

        foreach ($this->siteaccessRootLocationIdMap as $siteaccess => $rootLocationId) {
            if ($this->isSiteaccessExcluded($siteaccess)) {
                continue;
            }

            if (in_array($rootLocationId, $ancestorAndSelfLocationIds, true)) {
                $set[$siteaccess] = true;
            }
        }

        $this->locationIdSiteaccessSetMap[$location->id] = $set;

        return $set;
    }

    private function initializeSiteaccessRootLocationIdMap(): void
    {
        if ($this->siteaccessRootLocationIdMap !== null) {
            return;
        }

        $this->siteaccessRootLocationIdMap = [];

        foreach ($this->siteaccesses as $siteaccess) {
            if ($this->isSiteaccessExcluded($siteaccess)) {
                continue;
            }

            $rootLocationId = $this->configResolver->getParameter(
                'content.tree_root.location_id',
                null,
                $siteaccess
            );

            $this->siteaccessRootLocationIdMap[$siteaccess] = $rootLocationId;
        }
    }

    private function isSiteaccessExcluded(string $siteaccess): bool
    {
        if (array_key_exists($siteaccess, $this->excludedSiteaccessSet)) {
            return true;
        }

        $siteaccessGroups = $this->siteaccessGroupsBySiteaccess[$siteaccess] ?? [];

        foreach ($siteaccessGroups as $siteaccessGroup) {
            if (array_key_exists($siteaccessGroup, $this->excludedSiteaccessGroupSet)) {
                return true;
            }
        }

        return false;
    }
}
