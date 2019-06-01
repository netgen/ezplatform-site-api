<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Extension;

use Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\NodeVisitor\GetAttrExpressionReplacer;
use Twig\Extension\AbstractExtension;

class GetAttrExpressionExtension extends AbstractExtension
{
    public function getNodeVisitors()
    {
        return [
            new GetAttrExpressionReplacer(),
        ];
    }
}
