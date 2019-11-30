<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Controller;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Traits\PagerfantaTrait;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class Controller extends AbstractController
{
    use SearchResultExtractorTrait;
    use PagerfantaTrait;

    /**
     * Returns the root location object for current siteaccess configuration.
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function getRootLocation(): Location
    {
        return $this->getSite()->getLoadService()->loadLocation(
            $this->getSite()->getSettings()->rootLocationId
        );
    }

    /**
     * @return \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    public function getQueryTypeRegistry(): QueryTypeRegistry
    {
        /** @var \eZ\Publish\Core\QueryType\QueryTypeRegistry $registry */
        $registry = $this->container->get('ezpublish.query_type.registry');

        return $registry;
    }

    /**
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository(): Repository
    {
        /** @var \eZ\Publish\API\Repository\Repository $repository */
        $repository = $this->container->get('ezpublish.api.repository');

        return $repository;
    }

    /**
     * Returns the general helper service, exposed in Twig templates as "ezpublish" global variable.
     *
     * @return \eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper
     */
    public function getGlobalHelper(): GlobalHelper
    {
        /** @var \eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper $globalHelper */
        $globalHelper = $this->container->get('ezpublish.templating.global_helper');

        return $globalHelper;
    }

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSite(): Site
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Site $site */
        $site = $this->container->get('netgen.ezplatform_site.site');

        return $site;
    }

    /**
     * @return \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected function getConfigResolver(): ConfigResolverInterface
    {
        /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver */
        $configResolver = $this->container->get('ezpublish.config.resolver');

        return $configResolver;
    }

    protected function getNamedObjectProvider(): Provider
    {
        /** @var \Netgen\Bundle\EzPlatformSiteApiBundle\NamedObject\Provider $namedObjectProvider */
        $namedObjectProvider = $this->container->get('netgen.ezplatform_site.named_object_provider');

        return $namedObjectProvider;
    }
}
