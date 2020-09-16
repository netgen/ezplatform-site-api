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
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    /**
     * Site getter.
     */
    protected function getSite(): Site
    {
        return $this->site;
    }
}
