<?php


namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter as BaseUrlAliasRouter;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\URLAliasService;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\Core\MVC\Symfony\Routing\Generator\UrlAliasGenerator;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle;
use InvalidArgumentException;
use LogicException;
use Netgen\EzPlatformSiteApi\API\Values\Content;
use Netgen\EzPlatformSiteApi\API\Values\ContentInfo;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use function array_key_exists;
use function array_map;
use function class_exists;
use function in_array;

class UrlAliasRouter extends BaseUrlAliasRouter
{
    public const OVERRIDE_VIEW_ACTION = 'ng_content:viewAction';

    private $currentSiteaccess;
    private $siteaccessNames;
    private $siteaccessGroupsBySiteaccess;
    private $frontendSiteaccessNameRootLocationIdMap;
    private $locationIdFrontendSiteaccessNameSetMapCache = [];

    public function __construct(
        LocationService $locationService,
        URLAliasService $urlAliasService,
        ContentService $contentService,
        UrlAliasGenerator $generator,
        SiteAccess $currentSiteaccess,
        array $siteaccessNames,
        array $siteaccessGroupsBySiteaccess,
        RequestContext $requestContext,
        LoggerInterface $logger = null
    ) {
        parent::__construct($locationService, $urlAliasService, $contentService, $generator, $requestContext, $logger);

        $this->currentSiteaccess = $currentSiteaccess;
        $this->siteaccessNames = $siteaccessNames;
        $this->siteaccessGroupsBySiteaccess = $siteaccessGroupsBySiteaccess;
    }

    public function matchRequest(Request $request): array
    {
        $parameters = parent::matchRequest($request);
        $overrideViewAction = $this->configResolver->getParameter(
            'override_url_alias_view_action',
            'netgen_ez_platform_site_api'
        );

        if ($overrideViewAction) {
            $parameters['_controller'] = self::OVERRIDE_VIEW_ACTION;
        }

        return $parameters;
    }

    /**
     * @inheritdoc
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        $location = $this->resolveLocation($name, $parameters);
        $isCrossSiteaccessRoutingEnabled = $this->configResolver->getParameter('ng_cross_siteaccess_routing');

        if (!$isCrossSiteaccessRoutingEnabled) {
            return parent::generate($location, $parameters, $referenceType);
        }

        return $this->crossSiteaccessGenerate($location, $parameters, $referenceType);
    }

    public function supports($name): bool
    {
        return
            $name instanceof Content
            || $name instanceof ContentInfo
            || $name instanceof Location
            || parent::supports($name);
    }

    private function crossSiteaccessGenerate(APILocation $location, $parameters, $referenceType): string
    {
        $frontendSiteaccessName = $this->getFrontendSiteaccessNameForLocation($location);

        if ($frontendSiteaccessName === $this->currentSiteaccess->name) {
            return parent::generate($location, $parameters, $referenceType);
        }

        $parameters['siteaccess'] = $frontendSiteaccessName;

        return parent::generate($location, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param mixed $name
     * @param mixed $parameters
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    private function resolveLocation($name, $parameters): APILocation
    {
        if ($name instanceof Location) {
            return $name->innerLocation;
        }

        if ($name instanceof APILocation) {
            return $name;
        }

        if ($name instanceof Content || $name instanceof ContentInfo) {
            if (!$name->mainLocation instanceof Location) {
                throw new LogicException(
                    'Cannot generate an UrlAlias route for Content without the main Location'
                );
            }

            return $name->mainLocation->innerLocation;
        }

        if ($name !== self::URL_ALIAS_ROUTE_NAME) {
            throw new RouteNotFoundException('Could not match route');
        }

        return $this->resolveLocationFromParameters($parameters);
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    private function resolveLocationFromParameters(array $parameters): APILocation
    {
        if (isset($parameters['location']) && $parameters['location'] instanceof APILocation) {
            return $parameters['location'];
        }

        if (isset($parameters['locationId'])) {
            return $this->locationService->loadLocation($parameters['locationId']);
        }

        if (isset($parameters['contentId'])) {
            $contentInfo = $this->contentService->loadContentInfo($parameters['contentId']);

            if ($contentInfo->mainLocationId === null) {
                throw new LogicException(
                    'Cannot generate an UrlAlias route for content without the main Location'
                );
            }

            return $this->locationService->loadLocation($contentInfo->mainLocationId);
        }

        throw new InvalidArgumentException(
            'When generating an UrlAlias route, either "location", "locationId" or "contentId" parameter must be provided'
        );
    }

    private function getFrontendSiteaccessNameForLocation(APILocation $location): string
    {
        $nameSet = $this->getFrontendSiteaccessNameSet($location);

        if (empty($nameSet) || array_key_exists($this->currentSiteaccess->name, $nameSet)) {
            return $this->currentSiteaccess->name;
        }

        return array_key_first($nameSet);
    }

    private function getFrontendSiteaccessNameSet(APILocation $location): array
    {
        if (array_key_exists($location->id, $this->locationIdFrontendSiteaccessNameSetMapCache)) {
            return $this->locationIdFrontendSiteaccessNameSetMapCache[$location->id];
        }

        $ancestorLocationIds = array_map('\intval', $location->path);
        $map = $this->getFrontendSiteaccessNameRootLocationIdMap();
        $nameSet = [];

        foreach ($map as $siteaccessName => $rootLocationId) {
            if (in_array($rootLocationId, $ancestorLocationIds, true)) {
                $nameSet[$siteaccessName] = true;
            }
        }

        return $this->locationIdFrontendSiteaccessNameSetMapCache[$location->id] = $nameSet;
    }

    private function getFrontendSiteaccessNameRootLocationIdMap(): array
    {
        if ($this->frontendSiteaccessNameRootLocationIdMap !== null) {
            return $this->frontendSiteaccessNameRootLocationIdMap;
        }

        $this->frontendSiteaccessNameRootLocationIdMap = [];

        foreach ($this->siteaccessNames as $siteaccessName) {
            if ($this->isAdminSiteaccess($siteaccessName)) {
                continue;
            }

            $rootLocationId = $this->configResolver->getParameter(
                'content.tree_root.location_id',
                null,
                $siteaccessName
            );

            $this->frontendSiteaccessNameRootLocationIdMap[$siteaccessName] = $rootLocationId;
        }

        return $this->frontendSiteaccessNameRootLocationIdMap;
    }

    private function isAdminSiteaccess(string $siteaccessName): bool
    {
        $adminSiteaccessGroupName = 'admin_group';
        $ngAdminSiteaccessGroupName = 'ngadmin_group';
        $siteaccessGroups = $this->siteaccessGroupsBySiteaccess[$siteaccessName] ?? [];

        if (class_exists(EzPlatformAdminUiBundle::class)) {
            $adminSiteaccessGroupName = EzPlatformAdminUiBundle::ADMIN_GROUP_NAME;
        }

        return in_array($adminSiteaccessGroupName, $siteaccessGroups, true)
            || in_array($ngAdminSiteaccessGroupName, $siteaccessGroups, true);
    }
}
