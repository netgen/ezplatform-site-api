<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as BaseController;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use Netgen\EzPlatformSiteApi\API\Site;
use Netgen\EzPlatformSiteApi\API\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Traits\PagerfantaTrait;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;

abstract class Controller extends BaseController
{
    use SearchResultExtractorTrait;
    use PagerfantaTrait;

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
     * Returns the root location object for current siteaccess configuration.
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
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
        return $this->container->get('ezpublish.query_type.registry');
    }
}
