<?php

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\NodeVisitor;

use Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Node\GetAttributeExpression;
use Twig\Environment;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class GetAttributeExpressionOverride implements NodeVisitorInterface
{
    public function enterNode(Node $node, Environment $env)
    {
        if (get_class($node) !== GetAttrExpression::class) {
            return $node;
        }

        return new GetAttributeExpression($node);
    }

    public function leaveNode(Node $node, Environment $env)
    {
        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}
