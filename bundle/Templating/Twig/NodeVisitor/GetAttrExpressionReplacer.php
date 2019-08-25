<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\NodeVisitor;

use Netgen\Bundle\EzPlatformSiteApiBundle\Templating\Twig\Node\GetAttrExpressionDecorator;
use Twig\Environment;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class GetAttrExpressionReplacer implements NodeVisitorInterface
{
    public function enterNode(Node $node, Environment $env): Node
    {
        if (\get_class($node) !== GetAttrExpression::class) {
            return $node;
        }

        return new GetAttrExpressionDecorator($node);
    }

    public function leaveNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
