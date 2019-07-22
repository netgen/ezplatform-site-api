<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Routing;

use eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter as BaseUrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;

class UrlAliasRouter extends BaseUrlAliasRouter
{
    public const OVERRIDE_VIEW_ACTION = 'ng_content:viewAction';

    public function matchRequest(Request $request): array
    {
        $parameters = parent::matchRequest($request);
        $overrideViewAction = $this->configResolver->getParameter(
            'override_url_alias_view_action',
            'netgen_ez_platform_site_api'
        );

        if ($overrideViewAction) {
            $parameters['_controller'] = self::OVERRIDE_VIEW_ACTION;
        }

        return $parameters;
    }
}
