<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\View;

use eZ\Publish\Core\MVC\Symfony\View\LocationValueView as BaseLocationValueView;
use Netgen\EzPlatformSiteApi\API\Values\Location;

interface LocationValueView extends BaseLocationValueView
{
    /**
     * Returns the Site Location.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function getSiteLocation(): ?Location;
}
