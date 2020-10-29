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
use function array_keys;
use function array_map;
use function in_array;
use function reset;

/**
 * todo group boxes
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
    private $locationIdSiteaccessesMap = [];

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
        $siteaccesses = $this->getSiteaccesses($location);

        if (empty($siteaccesses)) {
            return $this->currentSiteaccess->name;
        }

        try {
            $availableLanguageSet = $this->getLanguageSet($location);
        } catch (Exception $e) {
            return $this->currentSiteaccess->name;
        }

        $currentSiteaccess = $this->currentSiteaccess->name;
        $alwaysAvailable = $location->contentInfo->alwaysAvailable;
        $currentPrioritizedLanguages = $this->getCurrentPrioritizedLanguages();

        $maybeSiteaccess = $this->matchTranslationSiteaccess(
            in_array($currentSiteaccess, $siteaccesses, true) ? $currentSiteaccess : null,
            $currentPrioritizedLanguages,
            $availableLanguageSet,
            $alwaysAvailable
        );

        if ($maybeSiteaccess !== null) {
            return $maybeSiteaccess;
        }

        $currentPrimaryLanguage = reset($currentPrioritizedLanguages);
        $siteaccessesWithSamePrimaryLanguage = $this->matchSiteaccessesByPrimaryLanguage($siteaccesses, $currentPrimaryLanguage);

        foreach ($siteaccessesWithSamePrimaryLanguage as $siteaccess) {
            $maybeSiteaccess = $this->matchTranslationSiteaccess(
                $siteaccess,
                $currentPrioritizedLanguages,
                $availableLanguageSet,
                $alwaysAvailable
            );

            if ($maybeSiteaccess !== null) {
                return $maybeSiteaccess;
            }
        }

        foreach ($currentPrioritizedLanguages as $language) {
            $matchSet = [];
            $this->matchSiteaccessesByBestPrioritizedLanguage($siteaccesses, $language, $matchSet);

            foreach (array_keys($matchSet) as $siteaccess) {
                $maybeSiteaccess = $this->matchTranslationSiteaccess(
                    $siteaccess,
                    $currentPrioritizedLanguages,
                    $availableLanguageSet,
                    $alwaysAvailable
                );

                if ($maybeSiteaccess !== null) {
                    return $maybeSiteaccess;
                }
            }
        }

        if ($alwaysAvailable) {
            return reset($siteaccesses);
        }

        return $currentSiteaccess;
    }

    private function matchTranslationSiteaccess(
        ?string $siteaccess,
        array $currentPrioritizedLanguages,
        array $availableLanguageSet,
        bool $alwaysAvailable
    ): ?string {
        if ($siteaccess === null) {
            return null;
        }

        foreach ($currentPrioritizedLanguages as $language) {
            if (!array_key_exists($language, $availableLanguageSet)) {
                continue;
            }

            $maybeTranslationSiteaccess = $this->matchTranslationSiteaccessByLanguage($siteaccess, $language);

            if ($maybeTranslationSiteaccess !== null) {
                return $maybeTranslationSiteaccess;
            }
        }

        if ($alwaysAvailable) {
            return $siteaccess;
        }

        if ($this->isSomeLanguageAllowed($siteaccess, $availableLanguageSet)) {
            return $siteaccess;
        }

        return null;
    }

    private function isSomeLanguageAllowed(string $siteaccess, array $availableLanguageSet): bool
    {
        $prioritizedLanguages = $this->getPrioritizedLanguages($siteaccess);

        foreach ($prioritizedLanguages as $language) {
            if (array_key_exists($language, $availableLanguageSet)) {
                return true;
            }
        }

        return false;
    }

    private function matchTranslationSiteaccessByLanguage(string $siteaccess, string $language): ?string
    {
        $translationSiteaccesses = $this->getTranslationSiteaccesses($siteaccess);

        foreach ($translationSiteaccesses as $translationSiteaccess) {
            if ($this->isSiteaccessExcluded($translationSiteaccess)) {
                continue;
            }

            $primaryLanguage = $this->getPrimaryLanguage($translationSiteaccess);

            if ($language === $primaryLanguage) {
                return $translationSiteaccess;
            }
        }

        return null;
    }

    /**
     * @param string[] $siteaccesses
     *
     * @return string[]
     */
    private function matchSiteaccessesByPrimaryLanguage(array $siteaccesses, string $language): array
    {
        $matched = [];

        foreach ($siteaccesses as $siteaccess) {
            $primaryLanguage = $this->getPrimaryLanguage($siteaccess);

            if ($language === $primaryLanguage) {
                $matched[] = $siteaccess;
            }
        }

        return $matched;
    }

    /**
     * @param string[] $siteaccesses
     */
    private function matchSiteaccessesByBestPrioritizedLanguage(
        array $siteaccesses,
        string $language,
        array &$matchSet,
        int $position = 0
    ): void {
        $continue = false;

        foreach ($siteaccesses as $siteaccess) {
            $prioritizedLanguages = $this->getPrioritizedLanguages($siteaccess);
            $positionedLanguage = $prioritizedLanguages[$position] ?? null;

            if ($language === $positionedLanguage) {
                $matchSet[$siteaccess] = true;
            }

            $continue = $continue || array_key_exists($position + 1, $prioritizedLanguages);
        }

        if ($continue) {
            $this->matchSiteaccessesByBestPrioritizedLanguage($siteaccesses, $language, $matchSet, ++$position);
        }
    }

    /**
     * @throws \Exception
     *
     * @return string[]
     */
    private function getLanguageSet(Location $location): array
    {
        $languages = $this->persistenceHandler->contentHandler()->loadVersionInfo($location->contentId)->languageCodes;

        return array_fill_keys($languages, true);
    }

    /**
     * @return string
     */
    private function getPrimaryLanguage(string $siteaccess): string
    {
        $prioritizedLanguages = $this->getPrioritizedLanguages($siteaccess);

        return reset($prioritizedLanguages);
    }

    /**
     * @return string[]
     */
    private function getPrioritizedLanguages(string $siteaccess): array
    {
        return $this->configResolver->getParameter('languages', null, $siteaccess);
    }

    /**
     * @return string[]
     */
    private function getCurrentPrioritizedLanguages(): array
    {
        return $this->getPrioritizedLanguages($this->currentSiteaccess->name);
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

    private function getSiteaccesses(Location $location): array
    {
        if (array_key_exists($location->id, $this->locationIdSiteaccessesMap)) {
            return $this->locationIdSiteaccessesMap[$location->id];
        }

        $ancestorAndSelfLocationIds = array_map('\intval', $location->path);
        $this->initializeSiteaccessRootLocationIdMap();
        $siteaccesses = [];

        foreach ($this->siteaccessRootLocationIdMap as $siteaccess => $rootLocationId) {
            if (in_array($rootLocationId, $ancestorAndSelfLocationIds, true)) {
                $siteaccesses[] = $siteaccess;
            }
        }

        $this->locationIdSiteaccessesMap[$location->id] = $siteaccesses;

        return $siteaccesses;
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
