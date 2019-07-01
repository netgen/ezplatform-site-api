<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as BaseController;
use Netgen\EzPlatformSiteApi\API\Site;
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
        return $this->container->get('netgen.ezplatform_site.site');
    }

    /**
     * Returns the root location object for current siteaccess configuration.
     *
     * @throws \Netgen\EzPlatformSiteApi\API\Exceptions\TranslationNotMatchedException
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function getRootLocation()
    {
        return $this->getSite()->getLoadService()->loadLocation(
            $this->getSite()->getSettings()->rootLocationId
        );
    }

    /**
     * @return \eZ\Publish\Core\QueryType\QueryTypeRegistry
     */
    public function getQueryTypeRegistry()
    {
        return $this->container->get('ezpublish.query_type.registry');
    }
}
