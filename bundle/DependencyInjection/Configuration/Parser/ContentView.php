<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\DependencyInjection\Configuration\Parser;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\ContentView as BaseContentView;

class ContentView extends BaseContentView
{
    const NODE_KEY = 'ngcontent_view';
    const INFO = 'Template selection settings when displaying a content with Netgen Site API';
}
