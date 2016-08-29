<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * @return \Netgen\EzPlatformSite\API\Site
     */
    public function getSite()
    {
        return $this->container->get('netgen.ezplatform_site.site');
    }

    /**
     * Returns the root location object for current siteaccess configuration.
     *
     * @return \Netgen\EzPlatformSite\API\Values\Location
     */
    public function getRootLocation()
    {
        $rootLocation = parent::getRootLocation();

        return $this->getSite()->getLoadService()->loadLocation($rootLocation->id);
    }
}
