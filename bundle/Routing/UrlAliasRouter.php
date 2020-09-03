<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter as BaseUrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;

class UrlAliasRouter extends BaseUrlAliasRouter
{
    public const OVERRIDE_VIEW_ACTION = 'ng_content::viewAction';

    public function matchRequest(Request $request): array
    {
        $parameters = parent::matchRequest($request);
        $isSiteApiPrimaryContentView = $this->configResolver->getParameter('ng_site_api.site_api_is_primary_content_view');

        if ($isSiteApiPrimaryContentView) {
            $parameters['_controller'] = self::OVERRIDE_VIEW_ACTION;
        }

        return $parameters;
    }
}
