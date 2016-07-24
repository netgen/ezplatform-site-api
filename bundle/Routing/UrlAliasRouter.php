<?php

namespace Netgen\EzPlatformSiteBundle\Routing;

use eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter as PlatformUrlAliasRouter;

class UrlAliasRouter extends PlatformUrlAliasRouter
{
    const VIEW_ACTION = 'ng_content:viewAction';
}
