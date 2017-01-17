<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\LocationValueView as BaseLocationValueView;

interface LocationValueView extends BaseLocationValueView
{
    /**
     * Returns the Site Location.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function getSiteLocation();
}
