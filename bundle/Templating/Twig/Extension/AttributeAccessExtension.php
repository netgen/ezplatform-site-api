<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\NodeVisitor\GetAttributeExpressionOverride;
use Twig\Extension\AbstractExtension;

class AttributeAccessExtension extends AbstractExtension
{
    public function getNodeVisitors()
    {
        return [
            new GetAttributeExpressionOverride(),
        ];
    }
}
