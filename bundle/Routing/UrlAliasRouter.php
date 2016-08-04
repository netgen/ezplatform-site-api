<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter as BaseUrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;

class UrlAliasRouter extends BaseUrlAliasRouter
{
    public function matchRequest(Request $request)
    {
        $parameters = parent::matchRequest($request);
        $override = $this->configResolver->getParameter(
            'override_url_alias_view_action',
            'netgen_ez_platform_site_api'
        );
        $viewAction = $this->configResolver->getParameter(
            'url_alias_view_action',
            'netgen_ez_platform_site_api'
        );

        if ($override && !empty($viewAction)) {
            $parameters['_controller'] = $viewAction;
        }

        return $parameters;
    }
}
