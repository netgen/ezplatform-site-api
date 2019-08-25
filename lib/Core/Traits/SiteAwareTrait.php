<?php

declare(strict_types=1);

namespace Netgen\EzPlatformSiteApi\Core\Traits;

use Netgen\EzPlatformSiteApi\API\Site;

trait SiteAwareTrait
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    protected $site;

    /**
     * Site setter.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    /**
     * Site getter.
     *
     * @return \Netgen\EzPlatformSiteApi\API\Site
     */
    protected function getSite(): Site
    {
        return $this->site;
    }
}
