<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as BaseController;
use Netgen\EzPlatformSiteApi\Core\Traits\PagerfantaFindServiceSearchAdaptersTrait;
use Netgen\EzPlatformSiteApi\Core\Traits\SearchResultExtractorTrait;

abstract class Controller extends BaseController
{
    use SearchResultExtractorTrait;
    use PagerfantaFindServiceSearchAdaptersTrait;

    /**
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    public function getSite()
    {
        return $this->container->get('netgen.ezplatform_site.site');
    }

    /**
     * Returns the root location object for current siteaccess configuration.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function getRootLocation()
    {
        return $this->getSite()->getLoadService()->loadLocation(
            $this->getConfigResolver()->getParameter('content.tree_root.location_id')
        );
    }
}
